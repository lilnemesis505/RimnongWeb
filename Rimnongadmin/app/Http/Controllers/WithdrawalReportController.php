<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaterialWithdrawal;

class WithdrawalReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    public function index()
    {
        $withdrawals = MaterialWithdrawal::with(['admin', 'stockMaterial'])
                                ->orderBy('withdraw_date', 'desc')
                                ->paginate(25); // 👈 หน้าหลักยังแบ่งหน้าเหมือนเดิม

        return view('layouts.report.withdrawals', compact('withdrawals'));
    }


    public function print()
    {
        // 👈 ดึงข้อมูลทั้งหมด ไม่แบ่งหน้า
        $withdrawals = MaterialWithdrawal::with(['admin', 'stockMaterial'])
                                ->orderBy('withdraw_date', 'desc')
                                ->get(); 

        // 👈 ส่งไปที่ View ใหม่สำหรับพิมพ์
        return view('layouts.report.withdrawals_print', compact('withdrawals'));
    }

}