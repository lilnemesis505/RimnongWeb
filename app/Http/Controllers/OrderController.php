<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Receipt;

// เพิ่ม use ImageKit
use ImageKit\ImageKit;

class OrderController extends Controller
{
    public function show($id)
    {
        $order = Order::with(['customer', 'employee', 'promotion', 'details.product'])->findOrFail($id);
        
        return view('layouts.history.detail', compact('order'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        if (is_null($order->em_id)) {
            // ลบรูปจาก ImageKit ถ้ามี slips_id
            if (!empty($order->slips_id)) {
                $imageKit = new ImageKit(
                    env('IMAGEKIT_PUBLIC_KEY'),
                    env('IMAGEKIT_PRIVATE_KEY'),
                    env('IMAGEKIT_URL_ENDPOINT')
                );
                try {
                    $imageKit->deleteFile($order->slips_id);
                } catch (\Exception $e) {
                    // สามารถ log error ได้ถ้าต้องการ
                }
            }

            $order->delete();
            return redirect()->route('history.index')->with('success', 'ลบรายการสั่งซื้อเรียบร้อยแล้ว');
        }

        return redirect()->route('history.index')->with('error', 'ไม่สามารถลบรายการที่ดำเนินการแล้วได้');
    }

    public function generateReceipt($id)
    {
        $order = Order::with(['customer', 'employee', 'promotion', 'details.product'])->findOrFail($id);

        // คำนวณราคาสุทธิ
        $subtotal = $order->details->sum('pay_total');
        $discount = $order->promotion->promo_discount ?? 0;
        $netTotal = $subtotal - $discount;

        // ค้นหาหรือสร้าง record ในตาราง receipt
        $receipt = Receipt::firstOrCreate(
            ['order_id' => $order->order_id],
            [
                're_date' => $order->receive_date,
                'price_total' => $netTotal,
            ]
        );

        return view('layouts.history.receipt', compact('order', 'receipt'));
    }
}