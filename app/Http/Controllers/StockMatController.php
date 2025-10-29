<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\Protype;
use ImageKit\ImageKit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon; 
use App\Models\StockAdjustment; // 👈 [เพิ่ม] Import โมเดล Log
use Illuminate\Support\Facades\DB; // 👈 [เพิ่ม] Import DB Transaction

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
     * บันทึก StockMat ใหม่ พร้อม Log
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mat_name'    => 'required|string|max:255',
            'type_id'     => 'required|integer|exists:protype,type_id',
            'quantity'    => 'required|integer|min:1',
            'exp_date'    => 'nullable|date|after_or_equal:today', 
            'unitcost'    => 'required|numeric|min:0',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg', 
        ]);
        
        $admin_id = session('admin_id'); // 👈 [เพิ่ม] ดึง ID Admin
        if (!$admin_id) {
            return redirect()->back()->withInput()->with('error', 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
        }

        DB::beginTransaction(); // 👈 [เพิ่ม] เริ่ม Transaction
        try {
            $dataToCreate = $validated;
            $dataToCreate['import_date'] = now();
            $dataToCreate['status']      = 0;
            $dataToCreate['remain']      = $validated['quantity'];
            
            unset($dataToCreate['image_upload']);
            $mat = StockMat::create($dataToCreate); // 1. สร้างสินค้า

            // 2. [เพิ่ม] บันทึก Log แรก
            StockAdjustment::create([
                'stock_mat_id' => $mat->mat_id,
                'admin_id'     => $admin_id,
                'amount'       => $mat->remain, // 👈 จำนวนที่เพิ่ม
                'adjust_date'  => now()
            ]);

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
                    // (Log error แต่ไม่ rollback transaction ถ้าแค่รูปไม่เข้า)
                    Log::error('ImageKit Upload Error (store): ' . $e->getMessage());
                }
            }
            
            DB::commit(); // 👈 [เพิ่ม] ยืนยันการบันทึก
            return redirect()->route('stock.index')->with('success', 'เพิ่มข้อมูลและบันทึกประวัติเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack(); // 👈 [เพิ่ม] ย้อนกลับหากล้มเหลว
            Log::error('Stock Store Transaction Error: ' . $e->getMessage());
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
     * อัปเดต StockMat พร้อม Log
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mat_name'    => 'required|string|max:255',
            'type_id'     => 'required|integer|exists:protype,type_id',
            'quantity'    => 'required|integer|min:0', 
            'exp_date'    => 'nullable|date',
            'remain'      => 'required|integer|min:0', 
            'unitcost'    => 'required|numeric|min:0',
            'status'      => 'required|in:0,2',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg',
            'add_stock'   => 'nullable|integer|min:0', 
        ]);
        
        $admin_id = session('admin_id'); // 👈 [เพิ่ม] ดึง ID Admin
        if (!$admin_id) {
            return redirect()->back()->withInput()->with('error', 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
        }
        
        DB::beginTransaction(); // 👈 [เพิ่ม] เริ่ม Transaction
        try {
            
            $mat = StockMat::findOrFail($id);
            $old_remain = $mat->remain; // 👈 [เพิ่ม] เก็บยอดเก่า
            
            $stockToAdd = (int) $request->input('add_stock', 0);
            $manualRemainInput = (int) $request->input('remain'); // 👈 ยอดที่ผู้ใช้กรอกในช่อง 'remain'

            $current_remain_in_logic = $old_remain; 
            $currentImportDate = $mat->import_date; // 👈 ดึงวันที่นำเข้าเดิม

            // Logic ที่ 1: ตรวจสอบการ "ปรับยอด" (ช่อง remain)
            $manual_change = $manualRemainInput - $old_remain;
            if ($manual_change != 0) {
                $current_remain_in_logic = $manualRemainInput; // อัปเดตยอด
                
                StockAdjustment::create([ // 👈 บันทึก Log
                    'stock_mat_id' => $mat->mat_id,
                    'admin_id'     => $admin_id,
                    'reason_type'  => 'ปรับยอด',
                    'amount'       => $manual_change, // 👈 จำนวนที่เปลี่ยน (บวก/ลบ)
                    'adjust_date'  => now()
                ]);
            }

            // Logic ที่ 2: ตรวจสอบการ "รับเข้าสต็อกเพิ่ม"
            if ($stockToAdd > 0) {
                $current_remain_in_logic += $stockToAdd; // บวกเพิ่ม
                
                StockAdjustment::create([ // 👈 บันทึก Log
                    'stock_mat_id' => $mat->mat_id,
                    'admin_id'     => $admin_id,
                    'amount'       => $stockToAdd, // 👈 จำนวนที่เพิ่ม (บวก)
                    'adjust_date'  => now()
                ]);
                
                // ตั้งค่าสำหรับอัปเดตตารางหลัก
                $validated['quantity'] = $mat->quantity + $stockToAdd;
                $currentImportDate = now();
                $validated['import_date'] = $currentImportDate;
            }
            
            // [แก้ไข] ยอดคงเหลือสุดท้าย
            $validated['remain'] = $current_remain_in_logic; 
            
            // (Logic การตรวจสอบ exp_date)
            if ($request->filled('exp_date')) {
                $expDate = Carbon::parse($request->input('exp_date'));
                if ($expDate->isBefore($currentImportDate)) {
                    // 👈 [สำคัญ] ต้องโยน Exception เพื่อให้ Transaction Rollback
                    throw new \Exception('วันหมดอายุต้องไม่ก่อนวันที่นำเข้าล่าสุด ('.$currentImportDate->format('m/d/Y').')');
                }
            }

            // (Logic การตัดสินใจ status)
            $statusFromForm = (int) $validated['status'];
            if ($statusFromForm == 2) {
                $validated['status'] = 2;
            } else {
                $validated['status'] = ($validated['remain'] > 0) ? 0 : 1;
            }

            // (Logic อัปโหลดรูปภาพ เหมือนเดิม)
            if ($request->hasFile('image_upload')) {
                // ... (try/catch ของ ImageKit) ...
            }
            
            unset($validated['add_stock']);
            $mat->update($validated); // 👈 อัปเดตตารางหลัก

            DB::commit(); // 👈 [เพิ่ม] ยืนยัน
            return redirect()->route('stock.index')->with('success', 'อัปเดตข้อมูลและบันทึกประวัติเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack(); // 👈 [เพิ่ม] ย้อนกลับ
            Log::error('Stock Update Transaction Error: ' . $e->getMessage());
            // [แก้ไข] ส่ง Error กลับไปที่ฟอร์ม
            return redirect()->back()->withInput()->withErrors(['update_error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * ลบ StockMat (Soft Delete) พร้อม Log
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

            // [เพิ่ม] บันทึก Log การลบ
            StockAdjustment::create([
                'stock_mat_id' => $mat->mat_id,
                'admin_id'     => $admin_id,
                'reason_type'  => 'ลบสินค้า',
'amount'       => -($mat->remain), // 👈 จำนวนที่ลบ (ติดลบ)
                'adjust_date'  => now()
            ]);
            
            // (ImageKit delete... เหมือนเดิม)
            if ($mat->image_id) {
                try {
                    $this->imageKit->deleteFile($mat->image_id);
                } catch (\Exception $e) {
                    Log::warning('ImageKit Delete Error (during destroy): ' . $e->getMessage());
                }
            }

            $mat->delete(); // 👈 นี่คือ Soft Delete

            DB::commit();
            return redirect()->route('stock.index')->with('success', 'ลบข้อมูลและบันทึกประวัติเรียบร้อยแล้ว');
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Destroy Transaction Error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'constraint violation')) {
                 return redirect()->route('stock.index')->with('error', 'ลบข้อมูลไม่สำเร็จ! (Constraint Error)');
            }
            return redirect()->route('stock.index')->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }
}