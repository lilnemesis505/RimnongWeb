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
   public function show(string $id)
    {

        $order = Order::with(['customer', 'employee', 'details.product', 'promotions'])->findOrFail($id);

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
            'promo_ids'   => 'nullable|string', // Expect a JSON string of an array
            'slip_image'  => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'pickup_time' => 'nullable|date_format:H:i',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        $orderItems = json_decode($request->order_items, true);
        if (!is_array($orderItems) || empty($orderItems)) {
            return response()->json(['status' => 'error', 'message' => 'Order items are invalid.'], 422);
        }

        $promoIds = $request->filled('promo_ids') ? json_decode($request->promo_ids, true) : [];
        if (!is_array($promoIds)) {
             return response()->json(['status' => 'error', 'message' => 'Promo IDs are invalid.'], 422);
        }


        DB::beginTransaction();
        try {
            $orderDate = Carbon::now();
            if ($request->filled('pickup_time')) {
                $pickupTime = Carbon::createFromFormat('H:i', $request->pickup_time);
                $orderDate = $pickupTime->subMinutes(10);
            }

            // Create the order without promo_id
            $order = Order::create([
                'cus_id'      => $request->cus_id,
                'order_date'  => $orderDate,
                'price_total' => $request->price_total,
                'remarks'     => $request->remarks ?? '',
                'grab_date'   => null,
            ]);

            // Attach all promotions to the order via the pivot table
            if (!empty($promoIds)) {
                $order->promotions()->attach($promoIds);
            }

            foreach ($orderItems as $item) {
                OrderDetail::create([
                    'order_id'   => $order->order_id,
                    'pro_id'     => $item['pro_id'],
                    'amount'     => $item['amount'],
                    'price_list' => $item['price_list'],
                    'pay_total'  => $item['pay_total'],
                ]);
            }

            // ImageKit upload logic
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
    // ... other methods like getPendingOrders(), updateStatus() etc. remain the same ...
   public function getPendingOrders()
{
    $orders = Order::with([
        'customer:cus_id,fullname', 
        'promotions:promo_id,promo_name',
        'details.product:pro_id,pro_name',
        'employee:em_id,em_name' // ✅ เพิ่มบรรทัดนี้เข้าไป
    ])
    ->whereNull('grab_date') 
    ->orderBy('order_date', 'asc')
    ->get();
            
    return response()->json($orders);
}
    
     public function updateStatus(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|integer|exists:order,order_id',
        'action' => 'required|string|in:accept,complete,pickup',
        'em_id' => 'required|integer|exists:employee,em_id',
    ]);

    $order = Order::find($validated['order_id']);

    // ✅ [REVISED LOGIC] ปรับปรุงโค้ดทั้งหมดด้านล่าง
    switch ($validated['action']) {
        case 'accept':
            if (is_null($order->em_id)) {
                $order->em_id = $validated['em_id'];
                $message = 'รับออเดอร์ #${order_id} สำเร็จ';
                $status = 'success';
            } else {
                // ถ้ามี em_id อยู่แล้ว (คนอื่นรับไปแล้ว)
                $message = 'ออเดอร์ #${order_id} ถูกรับโดยพนักงานคนอื่นแล้ว';
                $status = 'error'; // ส่งสถานะ error กลับไป
            }
            break;
        
       case 'complete':
    $order->receive_date = Carbon::now();
    $this->createReceiptForCompletedOrder($order); // ✅ เรียกใช้ฟังก์ชันสร้างใบเสร็จ
    $message = 'ทำรายการ #${order_id} เสร็จสิ้น';
    $status = 'success';
    break;

            break;

        case 'pickup':
            $order->grab_date = Carbon::now();
            $message = 'ลูกค้ารับสินค้า #${order_id} แล้ว';
            $status = 'success';
            break;

        default:
            $message = 'Action ไม่ถูกต้อง';
            $status = 'error';
    }

    // บันทึกเฉพาะเมื่อมีการเปลี่ยนแปลงและสถานะเป็น success
    if ($status === 'success') {
        $order->save();
    }

    // ถ้า status เป็น error ให้ส่ง http status code 409 (Conflict) กลับไป
    $httpStatusCode = $status === 'success' ? 200 : 409;

    return response()->json([
        'status' => $status,
        'message' => str_replace('${order_id}', $order->order_id, $message)
    ], $httpStatusCode);
}
    private function createReceiptForCompletedOrder(Order $order)
    {
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
    $order = Order::with(['customer', 'employee', 'promotions', 'details.product'])->findOrFail($id);

    // ✅ ถ้าออเดอร์เสร็จแล้ว แต่ยังไม่มีใบเสร็จ ให้สร้างขึ้นมาใหม่
    if ($order->receive_date && !$order->receipt) {
        $this->createReceiptForCompletedOrder($order);
        // ดึงข้อมูล order ใหม่อีกครั้งเพื่อให้ BIND relationship ของ receipt
        $order = Order::with(['customer', 'employee', 'promotions', 'details.product', 'receipt'])->findOrFail($id);
    }

    $receipt = $order->receipt;

    if (!$receipt) {
         return redirect()->back()->with('error', 'ไม่สามารถสร้างใบเสร็จได้เนื่องจากรายการยังไม่เสร็จสิ้น');
    }

    return view('layouts.history.receipt', compact('order', 'receipt'));
}
    public function getCustomerHistory($cusId)
{
    $orders = Order::with([
            'promotions:promo_id,promo_name', 
            'employee:em_id,em_name',         
            'details.product:pro_id,pro_name'
        ])
        ->where('cus_id', $cusId)
        ->orderBy('order_date', 'desc')
        ->get();

    return response()->json($orders);
}
}

