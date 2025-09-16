<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\StockMat;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $notifications = [];
        $today = Carbon::today();

        // === การแจ้งเตือนที่แสดงผลทั้งพนักงานและลูกค้า ===
        $activePromotions = Promotion::where('promo_start', '<=', $today)
                                     ->where('promo_end', '>=', $today)
                                     ->get();
        foreach ($activePromotions as $promo) {
            $notifications[] = [
                'type' => 'promotion',
                'title' => $promo->promo_name,
                'subtitle' => 'ส่วนลด ' . $promo->promo_discount . ' บาท',
                'timestamp' => $promo->promo_end,
            ];
        }

        // === การแจ้งเตือนเฉพาะลูกค้า (ถ้ามีการส่ง cus_id มา) ===
        if ($request->has('cus_id')) {
            $cusId = $request->cus_id;

            // ✅ [ADD] ดึงออเดอร์ของลูกค้าที่พร้อมรับแล้ว
            $readyToPickupOrders = Order::where('cus_id', $cusId)
                                        ->whereNotNull('receive_date') // ทำเสร็จแล้ว
                                        ->whereNull('grab_date')      // แต่ยังไม่มารับ
                                        ->get();

            foreach ($readyToPickupOrders as $order) {
                $notifications[] = [
                    'type' => 'ready_for_pickup', // <--- ประเภทใหม่
                    'title' => 'ออเดอร์ #' . $order->order_id . ' พร้อมรับแล้ว',
                    'subtitle' => 'กรุณาไปรับสินค้าที่ร้าน',
                    'timestamp' => $order->receive_date,
                ];
            }
        }
        // === การแจ้งเตือนเฉพาะพนักงาน (ถ้าไม่มีการส่ง cus_id) ===
        else {
            // 1. ดึงออเดอร์ใหม่ (ยังไม่มีพนักงานรับ)
            $newOrders = Order::whereNull('em_id')->orderBy('order_date', 'desc')->get();
            foreach ($newOrders as $order) {
                $notifications[] = [
                    'type' => 'new_order',
                    'title' => 'ออเดอร์ใหม่ #' . $order->order_id,
                    'subtitle' => 'ลูกค้า: ' . ($order->customer->fullname ?? 'N/A'),
                    'timestamp' => $order->order_date,
                ];
            }

            // 2. ดึงออเดอร์ Pre-order ที่ใกล้ถึงเวลานัดรับ
            $upcomingPreorders = Order::whereNotNull('em_id')
                                      ->whereNull('receive_date')
                                      ->where('order_date', '>', Carbon::now())
                                      ->where('order_date', '<=', Carbon::now()->addMinutes(30))
                                      ->get();
            foreach ($upcomingPreorders as $order) {
                $notifications[] = [
                    'type' => 'upcoming_preorder',
                    'title' => 'ใกล้ถึงเวลารับ #' . $order->order_id,
                    'subtitle' => 'เวลานัดรับ: ' . Carbon::parse($order->order_date)->format('H:i') . ' น.',
                    'timestamp' => $order->order_date,
                ];
            }

            // 3. วัตถุดิบใกล้หมดอายุ
            $expirationLimitDate = $today->copy()->addDays(15);
            $expiringStock = StockMat::where('exp_date', '>=', $today)
                                       ->where('exp_date', '<=', $expirationLimitDate)
                                       ->where('remain', '>', 0)
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
        }
        
        // เรียงลำดับการแจ้งเตือนทั้งหมดตามเวลา
        usort($notifications, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return response()->json($notifications);
    }
}