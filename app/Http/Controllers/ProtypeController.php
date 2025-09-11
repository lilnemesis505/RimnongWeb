<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Protype;

class ProtypeController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'type_name' => 'required|string|max:50'
    ]);

    Protype::create([
        'type_name' => $request->type_name
    ]);

   return redirect()->route('product.index')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');
}
   public function create()
{
    $protypes = Protype::all(); // ดึงข้อมูลทั้งหมดจากฐานข้อมูล
    return view('layouts.products.protype', compact('protypes'));
}
public function destroy($id)
{
    $protype = Protype::findOrFail($id);
    $protype->delete();

  return redirect()->route('product.index')->with('success', 'แก้ไขข้อมูลเรียบร้อยแล้ว');

}



}
