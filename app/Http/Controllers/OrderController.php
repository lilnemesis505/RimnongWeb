<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
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
            if (!empty($order->slips_id)) {
                $imageKit = new ImageKit(
                    env('IMAGEKIT_PUBLIC_KEY'),
                    env('IMAGEKIT_PRIVATE_KEY'),
                    env('IMAGEKIT_URL_ENDPOINT')
                );
                try {
                    $imageKit->deleteFile($order->slips_id);
                } catch (\Exception $e) {
                    Log::error('ImageKit delete failed: ' . $e->getMessage());
                }
            }
            $order->delete();
            return redirect()->route('history.index')->with('success', 'ลบรายการสั่งซื้อเรียบร้อยแล้ว');
        }

        return redirect()->route('history.index')->with('error', 'ไม่สามารถลบรายการที่ดำเนินการแล้วได้');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cus_id'      => 'required|integer|exists:customer,cus_id',
            'price_total' => 'required|numeric',
            'order_items' => 'required|string',
            'promo_id'    => 'nullable|integer|exists:promotion,promo_id',
            'slip_image'  => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $orderItems = json_decode($request->order_items, true);
        if (!is_array($orderItems) || empty($orderItems)) {
            return response()->json(['status' => 'error', 'message' => 'Order items are invalid.'], 422);
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'cus_id'      => $request->cus_id,
                'order_date'  => Carbon::now(),
                'promo_id'    => $request->promo_id,
                'price_total' => $request->price_total,
                'remarks'     => $request->remarks ?? '',
            ]);

            foreach ($orderItems as $item) {
                OrderDetail::create([
                    'order_id'   => $order->order_id,
                    'pro_id'     => $item['pro_id'],
                    'amount'     => $item['amount'],
                    'price_list' => $item['price_list'],
                    'pay_total'  => $item['pay_total'],
                ]);
            }

            $imageKit = new ImageKit(
                env('IMAGEKIT_PUBLIC_KEY'),
                env('IMAGEKIT_PRIVATE_KEY'),
                env('IMAGEKIT_URL_ENDPOINT')
            );

            $file = $request->file('slip_image');
            $fileName = 'slip_' . $order->order_id . '.' . $file->getClientOriginalExtension();

            $uploadResult = $imageKit->uploadFile([
                'file'     => base64_encode(file_get_contents($file->getRealPath())),
                'fileName' => $fileName,
                'folder'   => '/Slips',
            ]);

            if (isset($uploadResult->result)) {
                $order->slips_url = $uploadResult->result->url;
                $order->slips_id = $uploadResult->result->fileId;
                $order->save();
            } else {
                throw new \Exception('ImageKit upload failed.');
            }
            
            DB::commit();

            return response()->json(['status' => 'success', 'order_id' => $order->order_id], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order creation failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to save order.'], 500);
        }
    }

    public function getPendingOrders()
    {
        $orders = Order::with(['customer:cus_id,fullname', 'promotion:promo_id,promo_name', 'details.product:pro_id,pro_name'])
            ->whereNull('receive_date')
            ->orderBy('order_date', 'asc')
            ->get();
            
        return response()->json($orders);
    }
    
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|integer|exists:order,order_id',
            'action'   => 'required|string|in:accept,complete',
            'em_id'    => 'nullable|integer|exists:employee,em_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid data'], 422);
        }

        try {
            $order = Order::with('details.product', 'promotion')->findOrFail($request->order_id);

            if ($request->action === 'accept') {
                $order->em_id = $request->em_id;
                $order->save();
                return response()->json(['status' => 'success', 'message' => 'Order accepted successfully']);
            }
            
            if ($request->action === 'complete') {
                $order->receive_date = Carbon::now();
                $order->save();
                
                // ตรวจสอบและสร้างใบเสร็จสำหรับรายการที่เพิ่งทำเสร็จ
                $this->createReceiptForCompletedOrder($order);

                return response()->json(['status' => 'success', 'message' => 'Order completed successfully']);
            }
        } catch (\Exception $e) {
            Log::error('Order update failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to update order status'], 500);
        }
    }
    private function createReceiptForCompletedOrder(Order $order)
    {
        // ตรวจสอบว่ามีข้อมูลใน receipt แล้วหรือไม่
        if (!$order->receipt) {
            $subtotal = $order->details->sum('pay_total');
            $discount = $order->promotion->promo_discount ?? 0;
            $netTotal = $subtotal - $discount;

            Receipt::firstOrCreate(
                ['order_id' => $order->order_id],
                [
                    're_date' => $order->receive_date,
                    'price_total' => $netTotal,
                ]
            );
        }
    }


    public function generateReceipt($id)
    {
        $order = Order::with(['customer', 'employee', 'promotion', 'details.product'])->findOrFail($id);
        $receipt = $order->receipt;

        if (!$receipt) {
             return redirect()->back()->with('error', 'ไม่พบข้อมูลใบเสร็จ');
        }
        Log::info('Accessed existing receipt for Order ID: ' . $id);

        return view('layouts.history.receipt', compact('order', 'receipt'));
    }
      public function generateMissingReceipts()
    {
        $completedOrders = Order::whereNotNull('em_id')
                                ->whereNotNull('receive_date')
                                ->get();
        
        $missingReceiptsCount = 0;
        foreach ($completedOrders as $order) {
            try {
                if (!$order->receipt) {
                    $this->createReceiptForCompletedOrder($order);
                    $missingReceiptsCount++;
                }
            } catch (\Exception $e) {
                Log::error('Error generating missing receipt for order ID ' . $order->order_id . ': ' . $e->getMessage());
            }
        }
        
        return response()->json([
            'status' => 'success',
            'message' => "Generated {$missingReceiptsCount} missing receipts."
        ]);
    }

    public function getCustomerHistory($cusId)
    {
        $orders = Order::with([
                'customer:cus_id,fullname',
                'promotion:promo_id,promo_name',
                'details.product:pro_id,pro_name'
            ])
            ->where('cus_id', $cusId)
            ->orderBy('order_date', 'desc')
            ->get();

        return response()->json($orders);
    }
}