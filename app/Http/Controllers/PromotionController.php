<?php
namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
class PromotionController extends Controller
{
    public function index()
{
    $promotions = Promotion::paginate(10); // แสดง 10 รายการต่อหน้า
    return view('layouts.promotion.promotion', compact('promotions'));
}
public function create()
{
    return view('layouts.promotion.add');
}


    public function store(Request $request)
    {
        $request->validate([
            'promo_name' => 'required|string|max:50',
            'promo_discount' => 'required|numeric',
            'promo_start' => 'required|date',
            'promo_end' => 'required|date|after_or_equal:promo_start',
        ]);

        Promotion::create($request->all());
        return redirect()->back()->with('success', 'เพิ่มโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        Promotion::destroy($id);
        return redirect()->back()->with('success', 'ลบโปรโมชั่นเรียบร้อยแล้ว');
    }
      public function check(Request $request)
    {
        // 1. ตรวจสอบข้อมูลที่ส่งมาว่ามี 'promo_name' หรือไม่
        $validator = Validator::make($request->all(), [
            'promo_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request'
            ], 400); // 400 Bad Request
        }

        $promoName = $request->input('promo_name');
        $currentTime = Carbon::now();

        // 2. ค้นหาโปรโมชั่นด้วย Eloquent ORM
        $promotion = Promotion::where('promo_name', $promoName)
            ->where('promo_start', '<=', $currentTime)
            ->where('promo_end', '>=', $currentTime)
            ->first(); // ค้นหาแค่ 1 รายการ

        // 3. ตรวจสอบผลลัพธ์และส่ง JSON กลับ
        if ($promotion) {
            // ถ้าเจอโปรโมชั่นที่ใช้งานได้
            return response()->json([
                'status'         => 'success',
                'message'        => 'Promotion code applied successfully',
                'promo_discount' => $promotion->promo_discount,
                'promo_id'       => $promotion->promo_id,
            ]);
        } else {
            // ถ้าไม่เจอ หรือโปรโมชั่นหมดอายุ
            return response()->json([
                'status'  => 'error',
                'message' => 'Promotion code is invalid or expired',
            ], 404); // 404 Not Found
        }
    }
}
