<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\Protype;
use App\Http\Controllers\ProtypeController;


class StockMatController extends Controller
{
    public function index()
    {
        $stock_mats = StockMat::with('type')->paginate(20);
        return view('layouts.stock.stock', compact('stock_mats'));
        
    }

    public function create()
{
    $types = Protype::all(); // ดึงประเภทวัสดุทั้งหมด
    return view('layouts.stock.add', compact('types'));
}
public function store(Request $request)
{
    // ตรวจสอบข้อมูล
    $validated = $request->validate([
        'mat_name'    => 'required|string|max:255',
        'type_id'     => 'required|integer|exists:protype,type_id',
        'import_date' => 'required|date',
        'quantity'    => 'required|integer|min:1',
        'exp_date'    => 'nullable|date|after_or_equal:import_date',
        'remain'      => 'required|integer|min:0',
        'unitcost'    => 'required|numeric|min:0',
        'status'      => 'required|in:0,1,2',
    ]);

    // บันทึกข้อมูล
    StockMat::create($validated);

    // กลับไปหน้ารายการพร้อมข้อความแจ้งเตือน
    return redirect()->route('stock.index')->with('success', 'เพิ่มข้อมูลวัสดุเรียบร้อยแล้ว');
}
public function edit($id)
{
    $mat = StockMat::findOrFail($id);
    return view('layouts.stock.edit', compact('mat'));
}


    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
            'remain' => 'required|integer|min:0',
            'unitcost' => 'required|numeric|min:0',
            'status' => 'required|in:0,1,2',
        ]);

        $mat = StockMat::findOrFail($id);
        $mat->quantity = $request->quantity;
        $mat->remain = $request->remain;
        $mat->unitcost = $request->unitcost;
        $mat->status = $request->status;
        $mat->save();

        return redirect()->route('stock.index')->with('success', 'อัปเดตข้อมูลเรียบร้อยแล้ว');
    }
    public function destroy($id)
{
    $mat = StockMat::findOrFail($id);
    $mat->delete();

    return redirect()->route('stock.index')->with('success', 'ลบข้อมูลวัสดุเรียบร้อยแล้ว');
}

}





