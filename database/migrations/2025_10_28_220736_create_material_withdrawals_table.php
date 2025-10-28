<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMat;
use App\Models\MaterialWithdrawal;
use Illuminate\Support\Facades\Auth; // ใช้ Auth เพื่อดึง admin_id
use Illuminate\Support\Facades\DB; // ใช้ DB Transaction
use Illuminate\Validation\Rule; // ใช้ Rule สำหรับ Validation

class WithdrawalController extends Controller
{
    /**
     * แสดงหน้าฟอร์มเบิกวัตถุดิบ
     */
    public function create()
    {
        // ดึงเฉพาะวัตถุดิบที่ยังเหลืออยู่ (remain > 0)
        $stockMaterials = StockMat::where('remain', '>', 0)
                                  ->with('type') // โหลด type มาด้วย
                                  ->orderBy('mat_name')
                                  ->get();

        return view('layouts.stock.withdraw', compact('stockMaterials'));
    }

    /**
     * บันทึกการเบิกวัตถุดิบ
     */
    public function store(Request $request)
    {
        // 1. Validation เบื้องต้น
        $request->validate([
            'mat_id' => 'required|integer|exists:stock_mat,mat_id',
            'withdraw_amount' => 'required|integer|min:1',
        ], [
            'mat_id.required' => 'กรุณาเลือกวัตถุดิบ',
            'withdraw_amount.required' => 'กรุณากรอกจำนวนที่ต้องการเบิก',
            'withdraw_amount.min' => 'จำนวนที่เบิกต้องมากกว่า 0',
        ]);

        // 2. ดึงข้อมูล Stock และตรวจสอบจำนวนคงเหลือ
        $stock = StockMat::findOrFail($request->mat_id);

        if ($request->withdraw_amount > $stock->remain) {
            return redirect()->back()
                       ->withInput() // ส่งข้อมูลเก่ากลับไป
                       ->withErrors(['withdraw_amount' => "จำนวนที่เบิก ($request->withdraw_amount) เกินจำนวนคงเหลือ ($stock->remain)"]);
        }

        // 3. คำนวณราคา
        $calculatedCost = $request->withdraw_amount * $stock->unitcost;

        // 4. ดึง ID ของ Admin ที่ Login อยู่
        // (ต้องมั่นใจว่าใช้ Guard 'admin' ตอน Login)
        $adminId = Auth::guard('admin')->id();
        if (!$adminId) {
             return redirect()->back()->withInput()->withErrors(['general' => 'ไม่พบข้อมูลผู้ใช้งาน']);
        }

        // 5. ใช้ Transaction เพื่อความปลอดภัย
        DB::beginTransaction();
        try {
            // 5.1 บันทึกประวัติการเบิก
            MaterialWithdrawal::create([
                'mat_id' => $stock->mat_id,
                'admin_id' => $adminId,
                'withdraw_amount' => $request->withdraw_amount,
                'calculated_cost' => $calculatedCost,
                // withdraw_date จะถูกใส่โดยอัตโนมัติ (ถ้าตั้ง default ใน DB)
                // หรือจะใส่ 'withdraw_date' => now() ตรงนี้ก็ได้
            ]);

            // 5.2 ลดจำนวนคงเหลือใน Stock
            $stock->remain -= $request->withdraw_amount;
            // (อาจจะเพิ่ม Logic เช็กสถานะตรงนี้ เช่น ถ้า remain = 0 ให้ status = 1)
            $stock->save();

            DB::commit(); // ยืนยันการเปลี่ยนแปลง

            return redirect()->route('withdraw.create') // กลับไปหน้าเบิก (หรือไปหน้าอื่น)
                       ->with('success', "เบิก '{$stock->mat_name}' จำนวน {$request->withdraw_amount} สำเร็จ (ราคา: " . number_format($calculatedCost, 2) . " บาท)");

        } catch (\Exception $e) {
            DB::rollBack(); // ยกเลิกการเปลี่ยนแปลง
            // Log::error('Withdrawal Error: ' . $e->getMessage()); // บันทึก Log (แนะนำ)
            return redirect()->back()->withInput()->withErrors(['general' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()]);
        }
    }

    // (อาจจะเพิ่มฟังก์ชัน index() สำหรับแสดงประวัติการเบิกทั้งหมดในอนาคต)
}