<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockAdjustment; // 👈 เปลี่ยนเป็น Model นี้

class StockAdjustmentReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    /**
     * แสดงหน้ารายงาน (แบบแบ่งหน้า)
     */
    public function index()
    {
        $adjustments = StockAdjustment::with(['admin', 'stockMat'])
                                ->orderBy('adjust_date', 'desc')
                                ->paginate(25); // 👈 แสดงหน้าละ 25 รายการ

        // 👈 เรียก View 'adjustments.blade.php'
        return view('layouts.report.adjustments', compact('adjustments'));
    }

    /**
     * สร้างหน้าสำหรับพิมพ์ (ดึงข้อมูลทั้งหมด)
     */
    public function print()
    {
        $adjustments = StockAdjustment::with(['admin', 'stockMat'])
                                ->orderBy('adjust_date', 'desc')
                                ->get(); 

        // 👈 เรียก View 'adjustments_print.blade.php'
        return view('layouts.report.adjustments_print', compact('adjustments'));
    }
}