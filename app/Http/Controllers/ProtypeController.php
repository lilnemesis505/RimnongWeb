<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Protype;

class ProtypeController extends Controller
{
   public function store(Request $request)
{
    // ✅ เพิ่มกฎ unique และข้อความแจ้งเตือน
    $request->validate([
        'type_name' => 'required|string|max:50|unique:protype,type_name'
    ], [
        'type_name.unique' => 'มีประเภทสินค้า "' . $request->type_name . '" อยู่ในระบบแล้ว'
    ]);

    Protype::create([
        'type_name' => $request->type_name
    ]);

   // ✅ แนะนำให้เปลี่ยน route และข้อความให้ตรงกับการทำงาน
   return redirect()->route('protype.add')->with('success', 'เพิ่มประเภทสินค้าเรียบร้อยแล้ว');
}

   public function create()
{
    $protypes = Protype::all(); // ดึงข้อมูลทั้งหมดจากฐานข้อมูล
    return view('layouts.products.protype', compact('protypes'));
}

}
