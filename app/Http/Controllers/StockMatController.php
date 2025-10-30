<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\Protype;
use ImageKit\ImageKit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Models\StockAdjustment; // 👈 Import โมเดล Log
use Illuminate\Support\Facades\DB; // 👈 Import DB Transaction

class StockMatController extends Controller
{
    protected $imageKit;

    public function __construct()
    {
        $this->imageKit = new ImageKit(
            config('imagekit.public_key'),
            config('imagekit.private_key'),
            config('imagekit.url_endpoint')
        );
    }

    public function index()
    {
        $stock_mats = StockMat::with('type')->paginate(20);
        return view('layouts.stock.stock', compact('stock_mats'));
    }

    public function create()
    {
        $types = Protype::all();
        return view('layouts.stock.add', compact('types'));
    }

    /**
     * บันทึก StockMat ใหม่ (ลบ Log ออก)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mat_name'    => 'required|string|max:255',
            'type_id'     => 'required|integer|exists:protype,type_id',
            'quantity'    => 'nullable|integer|min:1', // 👈 (ผมแก้เป็น min:0 ให้สอดคล้องกับครั้งก่อน)
            'exp_date'    => 'nullable|date|after_or_equal:today',
            'unitcost'    => 'required|numeric|min:0',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $admin_id = session('admin_id');
        if (!$admin_id) {
            return redirect()->back()->withInput()->with('error', 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
        }

       DB::beginTransaction();
        try {
            $dataToCreate = $validated;
            
            $initial_quantity = $validated['quantity'] ?? 0;

            $dataToCreate['quantity']    = $initial_quantity; 
            $dataToCreate['import_date'] = now();
            $dataToCreate['status']      = ($initial_quantity > 0) ? 0 : 1; 
            $dataToCreate['remain']      = $initial_quantity; 

            unset($dataToCreate['image_upload']);
            $mat = StockMat::create($dataToCreate); // 1. สร้างสินค้า

            // 2. [แก้ไข] 👈 ลบ Log การสร้างสินค้า (if block) ออกแล้ว
            
            // 3. (เหมือนเดิม) อัปโหลดรูปภาพ
            if ($request->hasFile('image_upload')) {
                try {
                    $file = $request->file('image_upload');
                    $fileName = 'Stock' . $mat->mat_id . '.' . $file->getClientOriginalExtension();
                    $uploadResult = $this->imageKit->uploadFile([
                        'file'     => base64_encode(file_get_contents($file->getRealPath())),
                        'fileName' => $fileName,
                        'folder'   => '/Stock',
                        'useUniqueFileName' => false,
                    ]);
                    $mat->image = $uploadResult->result->url;
                    $mat->image_id = $uploadResult->result->fileId;
                    $mat->save();
                } catch (\Exception $e) {
                    Log::error('ImageKit Upload Error (store): ' . $e->getMessage());
                }
            }

            DB::commit();
            return redirect()->route('stock.index')->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว'); // (แก้ข้อความ)

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Store Transaction Error: '. $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $mat = StockMat::findOrFail($id);
        $types = Protype::all();
        return view('layouts.stock.edit', compact('mat', 'types'));
    }

    /**
     * [แก้ไข] อัปเดต StockMat (เหลือ Log เฉพาะปรับยอด)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mat_name'          => 'required|string|max:255',
            'type_id'           => 'required|integer|exists:protype,type_id',
            'quantity'          => 'nullable|integer|min:0', 
            'exp_date'          => 'nullable|date',
            'unitcost'          => 'required|numeric|min:0',
            'status'            => 'required|in:0,2',
            'image_upload'      => 'nullable|image|mimes:jpeg,png,jpg',
            'add_stock'         => 'nullable|integer|min:0',
            'adjustment_type'   => 'required|in:add,subtract',
            'adjustment_amount' => 'required|integer|min:0',
        ]);

        $admin_id = session('admin_id');
        if (!$admin_id) {
            return redirect()->back()->withInput()->with('error', 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
        }

        DB::beginTransaction();
        try {
            
            $mat = StockMat::findOrFail($id);
            
            $current_remain_in_logic = $mat->remain; 
            $current_quantity = $mat->quantity; 
            $currentImportDate = $mat->import_date;

            $stockToAdd = (int) $request->input('add_stock', 0);
            
            $adjustmentType = $request->input('adjustment_type', 'add');
            $adjustmentAmountInput = (int) $request->input('adjustment_amount', 0);
            
            $adjustmentAmount = ($adjustmentType == 'subtract') ? -$adjustmentAmountInput : $adjustmentAmountInput;

            
            // 3. Logic ที่ 1: ตรวจสอบการ "รับเข้าสต็อกเพิ่ม" (Import)
            if ($stockToAdd > 0) {
                $current_remain_in_logic += $stockToAdd; 
                $current_quantity = $stockToAdd; 
                $currentImportDate = now();
                
                // [แก้ไข] 👈 ลบ Log การรับเข้า (StockAdjustment::create) ออกแล้ว
                
                $validated['quantity'] = $current_quantity; 
                $validated['import_date'] = $currentImportDate;
            }

            // 4. Logic ที่ 2: ตรวจสอบการ "ปรับยอด" (Adjust)
            if ($adjustmentAmount != 0) {
                $current_remain_in_logic += $adjustmentAmount; 
                
                // [คงไว้] 👈 นี่คือ Log เดียวที่เหลืออยู่
                StockAdjustment::create([
                    'stock_mat_id' => $mat->mat_id,
                    'admin_id'     => $admin_id,
                    // [แก้ไข] 👈 ลบ 'reason_type' ออกเพื่อให้ตรงกับ Model
                    'amount'       => $adjustmentAmount,
                    'adjust_date'  => now()
                ]);
            }
            
            // 5. (ที่เหลือเหมือนเดิม...)
            $validated['remain'] = $current_remain_in_logic; 
            
            if ($request->filled('exp_date')) {
                $expDate = Carbon::parse($request->input('exp_date'));
                if ($expDate->isBefore($currentImportDate)) {
                    throw new \Exception('วันหมดอายุต้องไม่ก่อนวันที่นำเข้าล่าสุด ('.$currentImportDate->format('d/m/Y').')');
                }
            }

            $statusFromForm = (int) $validated['status'];
            if ($statusFromForm == 2) {
                $validated['status'] = 2; 
            } else {
                $validated['status'] = ($validated['remain'] > 0) ? 0 : 1; 
            }

            if ($request->hasFile('image_upload')) {
                if ($mat->image_id) {
                    try { $this->imageKit->deleteFile($mat->image_id); } catch (\Exception $e) { /* Log */ }
                }
                try {
                    $file = $request->file('image_upload');
                    $fileName = 'Stock' . $mat->mat_id . '.' . $file->getClientOriginalExtension();
                    $uploadResult = $this->imageKit->uploadFile([
                        'file'     => base64_encode(file_get_contents($file->getRealPath())),
                        'fileName' => $fileName,
                        'folder'   => '/Stock',
                        'useUniqueFileName' => false, 
                    ]);
                    $validated['image'] = $uploadResult->result->url;
                    $validated['image_id'] = $uploadResult->result->fileId;
                } catch (\Exception $e) {
                    Log::error('ImageKit Upload Error (update): ' . $e->getMessage());
                }
            }
            
            unset($validated['add_stock']);
            unset($validated['adjustment_type']);
            unset($validated['adjustment_amount']);
            
            $mat->update($validated); 

            DB::commit();
            return redirect()->route('stock.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว'); // (แก้ข้อความ)

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Update Transaction Error: '. $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['update_error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * ลบ StockMat (ลบ Log ออก)
     */
    public function destroy($id)
    {
        $admin_id = session('admin_id');
        if (!$admin_id) {
            return redirect()->back()->with('error', 'Session หมดอายุ');
        }
        
        DB::beginTransaction();
        try {
            $mat = StockMat::findOrFail($id);

            // [แก้ไข] 👈 ลบ Log การลบ (StockAdjustment::create) ออกแล้ว
            
            if ($mat->image_id) {
                try {
                    $this->imageKit->deleteFile($mat->image_id);
                } catch (\Exception $e) {
                    Log::warning('ImageKit Delete Error (during destroy): ' . $e->getMessage());
                }
            }

            $mat->delete(); // Soft Delete

            DB::commit();
            return redirect()->route('stock.index')->with('success', 'ลบข้อมูลเรียบร้อยแล้ว'); // (แก้ข้อความ)
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Destroy Transaction Error: '. $e->getMessage());
            
            if (str_contains($e->getMessage(), 'constraint violation')) {
                 return redirect()->route('stock.index')->with('error', 'ลบข้อมูลไม่สำเร็จ! (Constraint Error)');
            }
            return redirect()->route('stock.index')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}