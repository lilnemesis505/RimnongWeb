<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function saleReport(Request $request)
    {
        // กำหนด Query สำหรับดึงข้อมูลยอดขายสินค้า
        $query = OrderDetail::join('order', 'order_detail.order_id', '=', 'order.order_id')
            ->join('product', 'order_detail.pro_id', '=', 'product.pro_id')
            ->select(
                'product.pro_name as product_name',
                DB::raw('SUM(order_detail.amount) as total_amount'),
                DB::raw('SUM(order_detail.pay_total) as total_revenue')
            )
            ->whereNotNull('order.em_id')
            ->groupBy('product.pro_name')
            ->orderBy('total_amount', 'desc');

        // กำหนด Query สำหรับกราฟ
        $chartQuery = Order::select(
            DB::raw('DATE(receive_date) as date'),
            DB::raw('SUM(price_total) as total_sales')
        )
            ->whereNotNull('receive_date');

        $reportType = 'ยอดขายสินค้าทั้งหมด';
        $chartLabels = [];
        $chartData = [];

        // กำหนดเงื่อนไขการกรอง
        if ($request->filled('day_filter')) {
            $query->whereDate('order.receive_date', $request->day_filter);
            $chartQuery->whereDate('receive_date', $request->day_filter);
            $reportType = 'ยอดขายสินค้าประจำวัน ' . Carbon::parse($request->day_filter)->format('d/m/Y');

        } elseif ($request->filled('month_filter')) {
            $query->whereMonth('order.receive_date', $request->month_filter);
            $chartQuery->whereMonth('receive_date', $request->month_filter);
            $reportType = 'ยอดขายสินค้าประจำเดือน ' . Carbon::create(null, $request->month_filter)->format('F');

        } elseif ($request->filled('year_filter')) {
            $query->whereYear('order.receive_date', $request->year_filter);
            $chartQuery->whereYear('receive_date', $request->year_filter);
            $reportType = 'ยอดขายสินค้าประจำปี ' . $request->year_filter;
        }

        $reportData = $query->get();

        $chartData = $chartQuery->groupBy('date')->pluck('total_sales', 'date')->all();

        foreach ($chartData as $date => $total) {
            $chartLabels[] = $date;
            $chartData[] = $total;
        }

        return view('layouts.report.salereport', compact('reportData', 'reportType', 'chartLabels', 'chartData'));
    }
}
