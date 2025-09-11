<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Receipt;

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