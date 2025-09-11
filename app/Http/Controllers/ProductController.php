<?php


namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Protype;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // แสดงรายการสินค้า
    public function index()
    {
        $types = Protype::all();
        $products = Product::paginate(10); // แบ่งหน้า
        return view('layouts.products.product', compact('products','types'));
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
        $image->storeAs('products', $filename, 'public');
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

    //edit 
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
        // 🔥 ลบไฟล์เก่าทุกนามสกุลที่ชื่อ pro_id.*
        $files = Storage::files('public/products');
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_FILENAME) == $product->pro_id) {
                Storage::delete($file);
            }
        }

        // 📦 บันทึกรูปใหม่
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $filename = $product->pro_id . '.' . $extension;
        $image->storeAs('products', $filename, 'public');
    }

    $product->save();

    return redirect()->route('product.index')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
}



   public function destroy($pro_id)
{
    $product = Product::findOrFail($pro_id);

    // 🔥 ลบไฟล์ทั้งหมดที่ชื่อ pro_id.*
    $files = Storage::files('public/products');
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_FILENAME) == $product->pro_id) {
            Storage::delete($file);
        }
    }

    $product->delete();

    return redirect()->route('product.index')->with('success', 'ลบสินค้าสำเร็จแล้ว');
}

}
