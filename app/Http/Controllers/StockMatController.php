<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\Protype;
use ImageKit\ImageKit;
use Illuminate\Support\Facades\Log;

class StockMatController extends Controller
{
    protected $imageKit;

    public function __construct()
    {
        // (Constructor ถูกต้องแล้ว)
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

    // ✅ [FIX] แก้ไข Logic ทั้งหมดเพื่อรองรับการตั้งชื่อไฟล์ด้วย PK
    public function store(Request $request)
    {
        // 1. Validate ข้อมูลทั้งหมดก่อน
        $validated = $request->validate([
            'mat_name'    => 'required|string|max:255',
            'type_id'     => 'required|integer|exists:protype,type_id',
            'import_date' => 'required|date',
            'quantity'    => 'required|integer|min:1',
            'exp_date'    => 'nullable|date|after_or_equal:import_date',
            'remain'      => 'required|integer|min:0',
            'unitcost'    => 'required|numeric|min:0',
            'status'      => 'required|in:0,1,2',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg', 
        ]);

        // 2. เตรียมข้อมูลสำหรับสร้าง (ไม่รวมรูปภาพ)
        $dataToCreate = $validated;
        unset($dataToCreate['image_upload']); // ลบ key 'image_upload' ที่ไม่ใช่คอลัมน์ DB

        // 3. สร้าง StockMat "ก่อน" เพื่อให้ได้ PK (mat_id)
        $mat = StockMat::create($dataToCreate);

        // 4. ตรวจสอบว่ามีไฟล์อัปโหลดมาหรือไม่
        if ($request->hasFile('image_upload')) {
            try {
                $file = $request->file('image_upload');
                
                // ✅ [FIX] สร้างชื่อไฟล์ใหม่ตามที่คุณต้องการ
                $fileName = 'Stock' . $mat->mat_id . '.' . $file->getClientOriginalExtension();

                $uploadResult = $this->imageKit->uploadFile([
                    'file'     => base64_encode(file_get_contents($file->getRealPath())),
                    'fileName' => $fileName,
                    'folder'   => '/Stock',
                    'useUniqueFileName' => false, // 👈 สำคัญ: บังคับให้ใช้ชื่อไฟล์ของเรา
                ]);

                // 5. อัปเดตแถวที่สร้างไปแล้ว ด้วยข้อมูลรูปภาพ
                $mat->image = $uploadResult->result->url;
                $mat->image_id = $uploadResult->result->fileId;
                $mat->save();

            } catch (\Exception $e) {
                Log::error('ImageKit Upload Error (store): ' . $e->getMessage());
                // ถ้าอัปโหลดรูปไม่สำเร็จ ให้ส่งกลับไปพร้อมแจ้งเตือน
                // (StockMat ถูกสร้างแล้ว แต่ไม่มีรูป)
                return redirect()->route('stock.index')
                       ->with('success', 'เพิ่มวัสดุสำเร็จ แต่การอัปโหลดรูปภาพล้มเหลว: ' . $e->getMessage());
            }
        }

        return redirect()->route('stock.index')->with('success', 'เพิ่มข้อมูลวัสดุเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $mat = StockMat::findOrFail($id);
        $types = Protype::all(); 
        return view('layouts.stock.edit', compact('mat', 'types'));
    }

    // ✅ [FIX] แก้ไขชื่อไฟล์ตอนอัปเดต
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'mat_name'    => 'required|string|max:255',
            'type_id'     => 'required|integer|exists:protype,type_id',
            'import_date' => 'required|date',
            'quantity'    => 'required|integer|min:0',
            'exp_date'    => 'nullable|date|after_or_equal:import_date',
            'remain'      => 'required|integer|min:0',
            'unitcost'    => 'required|numeric|min:0',
            'status'      => 'required|in:0,1,2',
            'image_upload' => 'nullable|image|mimes:jpeg,png,jpg',
        ]);

        $mat = StockMat::findOrFail($id);

        if ($request->hasFile('image_upload')) {
            
            // 1. ลบรูปเก่า (ถ้ามี)
            if ($mat->image_id) {
                try {
                    $this->imageKit->deleteFile($mat->image_id);
                } catch (\Exception $e) {
                    Log::warning('ImageKit Delete Error (during update): ' . $e->getMessage());
                }
            }

            // 2. อัปโหลดรูปใหม่
            try {
                $file = $request->file('image_upload');
                
                // ✅ [FIX] สร้างชื่อไฟล์ใหม่ตามที่คุณต้องการ
                $fileName = 'Stock' . $mat->mat_id . '.' . $file->getClientOriginalExtension();

                $uploadResult = $this->imageKit->uploadFile([
                    'file'     => base64_encode(file_get_contents($file->getRealPath())),
                    'fileName' => $fileName,
                    'folder'   => '/Stock',
                    'useUniqueFileName' => false, // 👈 สำคัญ: บังคับให้ใช้ชื่อไฟล์ของเรา
                ]);

                // บันทึกข้อมูลรูปลงใน $validated (เพื่ออัปเดตทีเดียว)
                $validated['image'] = $uploadResult->result->url;
                $validated['image_id'] = $uploadResult->result->fileId;

            } catch (\Exception $e) {
                Log::error('ImageKit Upload Error (update): ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาดในการอัปโหลดรูปภาพใหม่');
            }
        }

        // 3. อัปเดตข้อมูล (รวมข้อมูลรูปภาพ ถ้ามี)
        $mat->update($validated);

        return redirect()->route('stock.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $mat = StockMat::findOrFail($id);

        // (Logic การลบถูกต้องอยู่แล้ว)
        if ($mat->image_id) {
            try {
                $this->imageKit->deleteFile($mat->image_id);
            } catch (\Exception $e) {
                Log::error('ImageKit Delete Error: ' . $e->getMessage());
            }
        }

        $mat->delete();

        return redirect()->route('stock.index')->with('success', 'ลบข้อมูลวัสดุเรียบร้อยแล้ว');
    }
}