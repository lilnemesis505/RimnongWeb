<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\MaterialWithdrawal;
use Illuminate\Support\Facades\Auth;
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
        ], [ 
            'mat_id.required' => 'กรุณาเลือกวัตถุดิบ',
            'withdraw_amount.required' => 'กรุณากรอกจำนวนที่ต้องการเบิก',
            'withdraw_amount.min' => 'จำนวนที่เบิกต้องมากกว่า 0',
        ]);

        $stock = StockMat::findOrFail($request->mat_id);

        if ($request->withdraw_amount > $stock->remain) {
            return redirect()->back()
                       ->withInput()
                       ->withErrors(['withdraw_amount' => "จำนวนที่เบิก ($request->withdraw_amount) เกินจำนวนคงเหลือ ($stock->remain)"]);
        }

        $calculatedCost = $request->withdraw_amount * $stock->unitcost;
        $adminId = session('admin_id');         

        if (!$adminId) {
             return redirect()->back()->withInput()->withErrors(['general' => 'ไม่พบข้อมูลผู้ใช้งานใน Session']);
        }

        DB::beginTransaction();
        try {
            // [แก้ไข] เพิ่ม 'withdraw_date' => now()
            MaterialWithdrawal::create([
                'mat_id' => $stock->mat_id,
                'admin_id' => $adminId, 
                'withdraw_amount' => $request->withdraw_amount,
                'calculated_cost' => $calculatedCost,
                'withdraw_date' => now() // 👈 เพิ่มบรรทัดนี้
            ]);

            $stock->remain -= $request->withdraw_amount;
            
            // อัปเดตสถานะ ถ้าของหมด
            if ($stock->remain <= 0 && $stock->status != 2) { 
                $stock->status = 1; // 1 = หมด
            }
            
            $stock->save();

            DB::commit();

            return redirect()->route('withdraw.create')
                       ->with('success', "เบิก '{$stock->mat_name}' จำนวน {$request->withdraw_amount} สำเร็จ (ราคา: " . number_format($calculatedCost, 2) . " บาท)");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['general' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()]);
        }
    }
    
  