<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockMat;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = [];

        // 1. ดึงออเดอร์ใหม่ (ยังไม่มีพนักงานรับ)
        // $newOrders = Order::whereNull('em_id')->orderBy('order_date', 'desc')->get();
        // foreach ($newOrders as $order) {
        //     $notifications[] = [
        //         'type' => 'new_order',
        //         'title' => 'ออเดอร์ใหม่ #' . $order->order_id,
        //         'subtitle' => 'ลูกค้า: ' . ($order->customer->fullname ?? 'N/A'),
        //         'timestamp' => $order->order_date,
        //     ];
        // }

        // 2. ดึงออเดอร์ Pre-order ที่ใกล้ถึงเวลานัดรับ (เช่น ใน 30 นาทีข้างหน้า)
        $upcomingPreorders = Order::whereNotNull('em_id') // รับแล้ว
                                  ->whereNull('receive_date') // ยังไม่เสร็จ
                                  ->where('order_date', '>', Carbon::now()) // เป็นออเดอร์อนาคต
                                  ->where('order_date', '<=', Carbon::now()->addMinutes(30)) // และใกล้ถึงเวลาใน 30 นาที
                                  ->get();
        foreach ($upcomingPreorders as $order) {
            $notifications[] = [
                'type' => 'upcoming_preorder',
                'title' => 'ใกล้ถึงเวลารับ #' . $order->order_id,
                'subtitle' => 'เวลานัดรับ: ' . Carbon::parse($order->order_date)->format('H:i') . ' น.',
                'timestamp' => $order->order_date,
            ];
        }

        // 3. ดึงโปรโมชั่นที่ใช้งานได้
        $today = Carbon::today();
        $activePromotions = Promotion::where('promo_start', '<=', $today)
                                     ->where('promo_end', '>=', $today)
                                     ->get();
        foreach ($activePromotions as $promo) {
            $notifications[] = [
                'type' => 'promotion',
                'title' => $promo->promo_name,
                'subtitle' => 'ส่วนลด ' . $promo->promo_discount . ' บาท',
                'timestamp' => $promo->promo_end, // ใช้เวลาสิ้นสุดเพื่อเรียงลำดับ
            ];
        }
         $expirationLimitDate = $today->copy()->addDays(15);
        $expiringStock = StockMat::where('exp_date', '>=', $today)
                                   ->where('exp_date', '<=', $expirationLimitDate)
                                   ->where('remain', '>', 0) // ดึงเฉพาะที่ยังมีของเหลือ
                                   ->get();

        foreach ($expiringStock as $stock) {
            $expDate = Carbon::parse($stock->exp_date);
            $remainingDays = $today->diffInDays($expDate);

            $notifications[] = [
                'type' => 'expiring_stock',
                'title' => 'เตือน: ' . $stock->mat_name,
                'subtitle' => 'เหลือ ' . $stock->remain . ' ชิ้น (หมดอายุใน ' . $remainingDays . ' วัน)',
                'timestamp' => $stock->exp_date,
            ];
        }
        
        // เรียงลำดับการแจ้งเตือนทั้งหมดตามเวลา
        usort($notifications, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return response()->json($notifications);
    }
}