<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\MaterialWithdrawal;
use Illuminate\Support\Facades\Auth; // (Use นี้อาจจะไม่จำเป็นแล้ว แต่เก็บไว้ก็ได้)
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WithdrawalController extends Controller
{

    public function create()
    {

        $stockMaterials = StockMat::where('remain', '>', 0)
                                  ->with('type')
                                  ->orderBy('mat_name')
                                  ->get();
        return view('layouts.stock.withdraw', compact('stockMaterials'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'mat_id' => 'required|integer|exists:stock_mat,mat_id',
            'withdraw_amount' => 'required|integer|min:1',
        ], [ /* ... messages ... */ ]);

        $stock = StockMat::findOrFail($request->mat_id);

        if ($request->withdraw_amount > $stock->remain) {
            return redirect()->back()
                       ->withInput()
                       ->withErrors(['withdraw_amount' => "จำนวนที่เบิก ($request->withdraw_amount) เกินจำนวนคงเหลือ ($stock->remain)"]);
        }

        $calculatedCost = $request->withdraw_amount * $stock->unitcost;


        $adminId = session('admin_id');         

        // ตรวจสอบว่าดึง ID จาก Session ได้หรือไม่
        if (!$adminId) {
             // (เปลี่ยน Error message เล็กน้อย)
             return redirect()->back()->withInput()->withErrors(['general' => 'ไม่พบข้อมูลผู้ใช้งานใน Session']);
        }

        // ... (ส่วน Transaction, Create, Save Stock เหมือนเดิม) ...
        DB::beginTransaction();
        try {
            MaterialWithdrawal::create([
                'mat_id' => $stock->mat_id,
                'admin_id' => $adminId, // ใช้ ID จาก Session
                'withdraw_amount' => $request->withdraw_amount,
                'calculated_cost' => $calculatedCost,
            ]);

            $stock->remain -= $request->withdraw_amount;
            $stock->save();

            DB::commit();

            return redirect()->route('withdraw.create')
                       ->with('success', "เบิก '{$stock->mat_name}' จำนวน {$request->withdraw_amount} สำเร็จ (ราคา: " . number_format($calculatedCost, 2) . " บาท)");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['general' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()]);
        }
    }
}