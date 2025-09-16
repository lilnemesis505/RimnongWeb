<?php
namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Product; // <-- 1. Import Model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    public function index()
    {
        // 2. ใช้ with('product') เพื่อดึงข้อมูลสินค้าที่ผูกกันมาด้วย (Eager Loading)
        $promotions = Promotion::with('product')->paginate(10);
        return view('layouts.promotion.promotion', compact('promotions'));
    }

    public function create()
    {
        // 3. ดึงข้อมูลสินค้าทั้งหมดเพื่อส่งไปให้ dropdown
        $products = Product::all();
        return view('layouts.promotion.add', compact('products'));
    }

   public function store(Request $request)
{
    $rules = [
        'promo_name'     => 'required|string|max:50', // กฎของ promo_name กลับมาเป็นแบบพื้นฐาน
        'promo_discount' => 'required|numeric',
        'promo_start'    => 'required|date',
        'promo_end'      => 'required|date|after_or_equal:promo_start',

        // ✅ 1. เปลี่ยนกฎของ pro_id ให้ตรวจสอบความซ้ำในตาราง promotion
        'pro_id' => [
            'required',
            'integer',
            'exists:product,pro_id',
            Rule::unique('promotion', 'pro_id'), // ตรวจสอบว่า pro_id นี้ต้องไม่ซ้ำในตาราง promotion
        ],
    ];

    $messages = [
        'pro_id.required' => 'กรุณาเลือกสินค้าที่ร่วมรายการ',
        'pro_id.exists'   => 'สินค้าที่เลือกไม่มีอยู่ในระบบ',
        // ✅ 2. เพิ่มข้อความแจ้งเตือนสำหรับ pro_id ที่ซ้ำกัน
        'pro_id.unique'   => 'สินค้านี้มีโปรโมชั่นอื่นอยู่แล้ว ไม่สามารถเพิ่มซ้ำได้',
    ];

    $request->validate($rules, $messages);

    Promotion::create($request->all());

    return redirect()->route('promotion.index')->with('success', 'เพิ่มโปรโมชั่นเรียบร้อยแล้ว');
}
  

    public function destroy($id)
    {
        Promotion::destroy($id);
        return redirect()->back()->with('success', 'ลบโปรโมชั่นเรียบร้อยแล้ว');
    }

    // ... (ฟังก์ชัน check ไม่มีการเปลี่ยนแปลง)
    public function check(Request $request)
    {
    }
    public function getActivePromotions()
    {
        $today = Carbon::today();

        $activePromotions = Promotion::where('promo_start', '<=', $today)
                                     ->where('promo_end', '>=', $today)
                                     ->orderBy('promo_end', 'asc')
                                     ->get();

        return response()->json($activePromotions);
    }
}
