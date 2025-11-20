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
        // [แก้ไข] 1. สร้าง $rules
        $rules = [
            'mat_name'    => 'required|string|max:255',
            'type_id'     => 'required|integer|exists:protype,type_id',
            // (ใช้ min:0 และ max:999999 สำหรับจำนวน)
            'quantity'    => 'nullable|integer|min:0|max:999999', 
            'exp_date'    => 'nullable|date|after_or_equal:today',
            'unitcost'    => 'required|numeric|min:0',
            // (max:3072 คือ 3MB)
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg|max:3072', 
        ];

        // [เพิ่ม] 2. สร้าง $messages สำหรับแจ้งเตือนภาษาไทย
        $messages = [
            'mat_name.required' => 'กรุณากรอกชื่อวัสดุ',
            'type_id.required'  => 'กรุณาเลือกประเภทวัสดุ',
            'quantity.max'      => 'จำนวนที่นำเข้ามากเกินไป (สูงสุด 999,999)',
            'quantity.min'      => 'จำนวนที่นำเข้าต้องไม่ติดลบ',
            'unitcost.required' => 'กรุณากรอกราคาต่อหน่วย',
            'image_upload.max'  => 'ขนาดรูปภาพต้องไม่เกิน 3MB',
            'image_upload.mimes'=> 'รองรับเฉพาะไฟล์ .jpg, .jpeg, .png เท่านั้น',
        ];

        // [แก้ไข] 3. ส่ง $rules และ $messages เข้าไป
        $validated = $request->validate($rules, $messages);

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
            $mat = StockMat::create($dataToCreate); 
            
            // (Logic อัปโหลดรูปภาพ ... เหมือนเดิม)
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
            return redirect()->route('stock.index')->with('success', 'เพิ่มข้อมูลเรียบร้อยแล้ว'); 

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
     * [แก้ไข] อัปเดต StockMat (เพิ่ม Validation Rules)
     */
    public function update(Request $request, $id)
    {
        // [แก้ไข] 1. ดึง StockMat มาก่อนเพื่อใช้เช็คยอดคงเหลือ
        $mat = StockMat::findOrFail($id);
        
        // [แก้ไข] 2. สร้าง $rules
        $rules = [
            'mat_name'          => 'required|string|max:255',
            'type_id'           => 'required|integer|exists:protype,type_id',
            'exp_date'          => 'nullable|date',
            'unitcost'          => 'required|numeric|min:0',
            'status'            => 'required|in:0,2',
            'image_upload'      => 'nullable|image|mimes:jpeg,png,jpg|max:3072', // 👈 (ข้อ 1)
            'add_stock'         => 'nullable|integer|min:0|max:999999', // 👈 (ข้อ 2)
            'adjustment_type'   => 'required|in:add,subtract',
            
            // [แก้ไข] 3. (ข้อ 3) เพิ่ม Custom Rule สำหรับ adjustment_amount
            'adjustment_amount' => [
                'required',
                'integer',
                'min:0',
                // ใช้ฟังก์ชัน Custom Rule
                function ($attribute, $value, $fail) use ($request, $mat) {
                    // ตรวจสอบเฉพาะเมื่อเลือก "ปรับลด"
                    if ($request->input('adjustment_type') == 'subtract') {
                        if ($value > $mat->remain) {
                            $fail("ไม่สามารถปรับลดได้ ยอดคงเหลือปัจจุบันคือ {$mat->remain}");
                        }
                    }
                },
            ],
        ];
        
        // [แก้ไข] 4. สร้าง $messages
        $messages = [
            'mat_name.required' => 'กรุณากรอกชื่อวัสดุ',
            'type_id.required'  => 'กรุณาเลือกประเภทวัสดุ',
            'unitcost.required' => 'กรุณากรอกราคาต่อหน่วย',
            'image_upload.max'  => 'ขนาดรูปภาพต้องไม่เกิน 3MB', // 👈 (ข้อ 1)
            'image_upload.mimes'=> 'รองรับเฉพาะไฟล์ .jpg, .jpeg, .png เท่านั้น',
            'add_stock.max'     => 'จำนวนนำเข้ามากเกินไป (สูงสุด 999,999)', // 👈 (ข้อ 2)
            'adjustment_amount.min' => 'จำนวนที่ปรับยอดต้องไม่ติดลบ',
            // (ข้อความสำหรับ Custom Rule จะถูกส่งโดย $fail)
        ];

        // [แก้ไข] 5. ทำการ Validate
        $validated = $request->validate($rules, $messages);
        
        $admin_id = session('admin_id');
        if (!$admin_id) {
            return redirect()->back()->withInput()->with('error', 'Session หมดอายุ กรุณาเข้าสู่ระบบใหม่');
        }

        DB::beginTransaction();
        try {
            // (ดึง $mat อีกครั้งภายใน Transaction เพื่อความปลอดภัย)
            $mat = StockMat::findOrFail($id);
            
            $current_remain_in_logic = $mat->remain; 
            $current_quantity = $mat->quantity; 
            $currentImportDate = $mat->import_date;

            $stockToAdd = (int) $request->input('add_stock', 0);
            $adjustmentType = $request->input('adjustment_type', 'add');
            $adjustmentAmountInput = (int) $request->input('adjustment_amount', 0);
            $adjustmentAmount = ($adjustmentType == 'subtract') ? -$adjustmentAmountInput : $adjustmentAmountInput;

            
            // (Logic การรับเข้า ... เหมือนเดิม)
            if ($stockToAdd > 0) {
                $current_remain_in_logic += $stockToAdd; 
                $current_quantity = $stockToAdd; 
                $currentImportDate = now();
                $validated['quantity'] = $current_quantity; 
                $validated['import_date'] = $currentImportDate;
            }

            // (Logic การปรับยอด ... เหมือนเดิม)
            if ($adjustmentAmount != 0) {
                $current_remain_in_logic += $adjustmentAmount; 
                StockAdjustment::create([
                    'stock_mat_id' => $mat->mat_id,
                    'admin_id'     => $admin_id,
                    'amount'       => $adjustmentAmount,
                    'adjust_date'  => now()
                ]);
            }
            
            $validated['remain'] = $current_remain_in_logic; 
            
            // (Logic ตรวจสอบวันที่ ... เหมือนเดิม)
            if ($request->filled('exp_date')) {
                // ...
            }

            // (Logic status ... เหมือนเดิม)
            $statusFromForm = (int) $validated['status'];
            // ...

            // (Logic อัปโหลดรูปภาพ ... เหมือนเดิม)
            if ($request->hasFile('image_upload')) {
                // ...
            }
            
            unset($validated['add_stock']);
            unset($validated['adjustment_type']);
            unset($validated['adjustment_amount']);
            
            $mat->update($validated); 

            DB::commit();
            return redirect()->route('stock.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock Update Transaction Error: '. $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['update_error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
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