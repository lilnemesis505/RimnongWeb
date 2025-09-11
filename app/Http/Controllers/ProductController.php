<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Protype;
use Illuminate\Http\Request;
use ImageKit\ImageKit; // เพิ่มบรรทัดนี้เพื่อเรียกใช้ ImageKit SDK
use Illuminate\Support\Facades\Log; // เพิ่มบรรทัดนี้เพื่อใช้ Log

class ProductController extends Controller
{
    protected $imageKit;

    public function __construct()
    {
        // สร้าง instance ของ ImageKit และดึง credentials จาก .env
        $this->imageKit = new ImageKit(
            config('imagekit.public_key'),
            config('imagekit.private_key'),
            config('imagekit.url_endpoint')
        );
    }
    
    // แสดงรายการสินค้า
    public function index()
    {
        $types = Protype::all();
        $products = Product::paginate(10); // แบ่งหน้า
        return view('layouts.products.product', compact('products', 'types'));
    }

    // ฟอร์มเพิ่มสินค้า
    public function create()
    {
        $types = Protype::all(); // ดึงประเภทสินค้าทั้งหมด
        return view('layouts.products.add', compact('types'));
    }

    // บันทึกสินค้าใหม่
    public function store(Request $request)
    {
        $request->validate([
            'pro_name' => 'required|string|max:50',
            'price' => 'required|numeric',
            'type_id' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);
    
        $data = $request->only('pro_name', 'price', 'type_id');
        $product = Product::create($data);
    
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = $product->pro_id . '.' . $extension;
    
            // 🔥 เปลี่ยนไปใช้อัปโหลดด้วย ImageKit
            $uploadResult = $this->imageKit->uploadFile([
                'file' => base64_encode(file_get_contents($image->getRealPath())),
                'fileName' => $filename,
                'folder' => '/products' // จัดเก็บในโฟลเดอร์ products บน ImageKit
            ]);
    
            if ($uploadResult['success']) {
                // อัปเดต URL ของรูปภาพลงในฐานข้อมูล
                $product->image = $uploadResult['result']['url'];
                $product->save();
            } else {
                Log::error('ImageKit upload failed: ' . json_encode($uploadResult['error']));
                return redirect()->back()->with('error', 'การอัปโหลดรูปภาพล้มเหลว');
            }
        }
    
        return redirect()->route('product.index')->with('success', 'เพิ่มสินค้าเรียบร้อยแล้ว');
    }

    public function filter(Request $request)
    {
        $typeId = $request->input('type_id');

        $query = Product::with('type');

        if ($typeId) {
            $query->where('type_id', $typeId);
        }

        $products = $query->paginate(12);
        $types = Protype::all();

        return view('layouts.products.product', compact('products', 'types'));
    }

    // edit
    public function edit($pro_id)
    {
        $product = Product::with('type')->findOrFail($pro_id);
        $types = Protype::all(); // ถ้ามีประเภทสินค้าให้เลือก
        return view('layouts.products.edit', compact('product', 'types'));
    }

    public function update(Request $request, $pro_id)
    {
        $request->validate([
            'pro_name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'type_id' => 'required|exists:protype,type_id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);
    
        $product = Product::findOrFail($pro_id);
        $product->pro_name = $request->pro_name;
        $product->price = $request->price;
        $product->type_id = $request->type_id;
    
        if ($request->hasFile('image')) {
            // 🔥 ลบไฟล์เก่าจาก ImageKit
            if ($product->image) {
                // ดึง File ID จาก URL ที่เก็บไว้
                $urlParts = explode('/', rtrim($product->image, '/'));
                $fileId = end($urlParts);
                
                try {
                    $this->imageKit->deleteFile($fileId);
                } catch (\Exception $e) {
                    Log::error("Failed to delete file from ImageKit: " . $e->getMessage());
                    // อาจจะไม่ต้องหยุดการทำงาน แต่ให้บันทึก log ไว้
                }
            }
    
            // 📦 บันทึกรูปใหม่ไปที่ ImageKit
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();
            $filename = $product->pro_id . '.' . $extension;
            
            $uploadResult = $this->imageKit->uploadFile([
                'file' => base64_encode(file_get_contents($image->getRealPath())),
                'fileName' => $filename,
                'folder' => '/products'
            ]);
    
            if ($uploadResult['success']) {
                $product->image = $uploadResult['result']['url'];
            } else {
                Log::error('ImageKit upload failed: ' . json_encode($uploadResult['error']));
                return redirect()->back()->with('error', 'การอัปโหลดรูปภาพล้มเหลว');
            }
        }
    
        $product->save();
    
        return redirect()->route('product.index')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
    }

    public function destroy($pro_id)
    {
        $product = Product::findOrFail($pro_id);

        // 🔥 ลบไฟล์จาก ImageKit
        if ($product->image) {
            // ดึง File ID จาก URL ที่เก็บไว้
            $urlParts = explode('/', rtrim($product->image, '/'));
            $fileId = end($urlParts);
            
            try {
                $this->imageKit->deleteFile($fileId);
            } catch (\Exception $e) {
                Log::error("Failed to delete file from ImageKit: " . $e->getMessage());
                // อาจจะไม่ต้องหยุดการทำงาน
            }
        }

        $product->delete();

        return redirect()->route('product.index')->with('success', 'ลบสินค้าสำเร็จแล้ว');
    }
}