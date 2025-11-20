<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Models\Product;

class ReportController extends Controller
{
    /**
     * แสดงรายงานยอดขายตามช่วงวันที่
     */
    public function saleReport(Request $request)
    {
        // 1. ตรวจสอบข้อมูล Input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = null;
        $endDate = null;
        $reportType = 'ยอดขายสินค้าทั้งหมด';

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $reportType = 'ยอดขายสินค้าตั้งแต่วันที่ ' . $startDate->format('d/m/Y') . ' ถึง ' . $endDate->format('d/m/Y');
        }

        // ✅ [REVISED] เปลี่ยน Query ของตารางสรุปให้เริ่มจาก OrderDetail
        // เพื่อให้ได้เฉพาะสินค้าที่ขายได้แล้วเท่านั้น
        $reportDataQuery = OrderDetail::join('order', 'order_detail.order_id', '=', 'order.order_id')
            ->join('product', 'order_detail.pro_id', '=', 'product.pro_id')
            ->select(
                'product.pro_name as product_name',
                DB::raw('SUM(order_detail.amount) as total_amount'),
                DB::raw('SUM(order_detail.pay_total) as total_revenue')
            )
            ->whereNotNull('order.receive_date');

        if ($startDate && $endDate) {
            $reportDataQuery->whereBetween('order.receive_date', [$startDate, $endDate]);
        }
        
        $reportData = $reportDataQuery->groupBy('product.pro_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // ส่วนของกราฟยังคงทำงานได้ดีเหมือนเดิม
        $baseQueryForChart = OrderDetail::join('order', 'order_detail.order_id', '=', 'order.order_id')
            ->join('product', 'order_detail.pro_id', '=', 'product.pro_id')
            ->whereNotNull('order.receive_date');

        if ($startDate && $endDate) {
            $baseQueryForChart->whereBetween('order.receive_date', [$startDate, $endDate]);
        }

        $chartRawData = $baseQueryForChart
            ->select(
                'product.pro_name',
                DB::raw('DATE("order".receive_date) as sale_date'),
                DB::raw('SUM(order_detail.pay_total) as daily_revenue')
            )
            ->groupBy('product.pro_name', 'sale_date')
            ->orderBy('sale_date', 'asc')
            ->get();

        // ประมวลผลข้อมูลสำหรับกราฟ (เหมือนเดิม)
        $chartLabels = $chartRawData->pluck('sale_date')->unique()->sort()->values();
        $productsData = $chartRawData->groupBy('pro_name');
        $chartDatasets = new Collection();
        foreach ($productsData as $productName => $data) {
            $salesLookup = $data->keyBy('sale_date');
            $dataPoints = $chartLabels->map(function ($date) use ($salesLookup) {
                return $salesLookup->get($date)->daily_revenue ?? 0;
            });
            $chartDatasets->push([
                'label' => $productName, 'data' => $dataPoints, 'tension' => 0.1, 'fill' => false,
            ]);
        }
        
        $formattedChartLabels = $chartLabels->map(function ($date) {
            return Carbon::parse($date)->format('d/m/Y');
        });

        return view('layouts.report.salereport', [
            'reportData' => $reportData,
            'reportType' => $reportType,
            'chartLabels' => $formattedChartLabels,
            'chartDatasets' => $chartDatasets,
        ]);
    }
     public function billReport(Request $request)
    {
        // 1. ตรวจสอบข้อมูล Input
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        // 2. สร้าง Query พื้นฐานสำหรับดึงข้อมูลใบเสร็จ
        $query = DB::table('receipt')
            ->join('order', 'receipt.order_id', '=', 'order.order_id')
            ->join('customer', 'order.cus_id', '=', 'customer.cus_id')
            ->leftJoin('employee', 'order.em_id', '=', 'employee.em_id') // ใช้ leftJoin เผื่อกรณีไม่มีพนักงาน
            ->select(
                'receipt.re_id',
                'receipt.re_date',
                'receipt.price_total',
                'receipt.order_id',
                'customer.fullname as customer_name',
                'employee.em_name as employee_name'
            );

        // 3. กรองข้อมูลตามช่วงวันที่ (ถ้ามี)
        $reportType = 'รายงานใบเสร็จทั้งหมด';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('receipt.re_date', [$startDate, $endDate]);
            $reportType = 'รายงานใบเสร็จตั้งแต่วันที่ ' . $startDate->format('d/m/Y') . ' ถึง ' . $endDate->format('d/m/Y');
        }

        // 4. คำนวณยอดรวมก่อน Paginate
        $totalRevenue = (clone $query)->sum('receipt.price_total');
        $receiptCount = (clone $query)->count();

        // 5. ดึงข้อมูลแบบแบ่งหน้า
        $reportData = $query->orderBy('receipt.re_date', 'desc')->paginate(15);

        // 6. ส่งข้อมูลไปยัง View
        return view('layouts.report.reportbill', [
            'reportData' => $reportData,
            'reportType' => $reportType,
            'totalRevenue' => $totalRevenue,
            'receiptCount' => $receiptCount,
        ]);
    }
}