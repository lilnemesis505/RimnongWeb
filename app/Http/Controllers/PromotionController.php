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
        $promotions = Promotion::with('product')
                                 ->withCount('orders') // 👈 [เพิ่ม] 1. สั่งให้นับ Order ล่วงหน้า
                                 ->paginate(10);
                                 
        return view('layouts.promotion.promotion', compact('promotions'));
    }

    public function create()
    {
        $products = Product::all();
        return view('layouts.promotion.add', compact('products'));
    }

   public function store(Request $request)
{
    // [แก้ไข] 1. ดึงราคาสินค้าออกมาก่อน
    $productId = $request->input('pro_id');
    $productPrice = 0; // ค่าเริ่มต้น

    if ($productId) {
        $product = Product::find($productId);
        if ($product) {
            $productPrice = $product->price; // 👈 ได้ราคาสินค้า
        }
    }

    // [แก้ไข] 2. สร้างกฎ Validation
    $rules = [
        'promo_name'     => 'required|string|max:50',
        'promo_start'    => 'required|date',
        'promo_end'      => 'required|date|after_or_equal:promo_start',
        'pro_id' => [
            'required',
            'integer',
            'exists:product,pro_id',
            Rule::unique('promotion', 'pro_id'), 
        ],
        // [แก้ไข] 3. เพิ่มกฎ min และ max (ตามราคาสินค้า)
        'promo_discount' => [
            'required',
            'numeric',
            'min:0',
            "max:{$productPrice}" // 👈 กฎใหม่: ห้ามเกินราคาสินค้า
        ],
    ];

    // [แก้ไข] 4. เพิ่มข้อความแจ้งเตือน
    $messages = [
        'pro_id.required' => 'กรุณาเลือกสินค้าที่ร่วมรายการ',
        'pro_id.exists'   => 'สินค้าที่เลือกไม่มีอยู่ในระบบ',
        'pro_id.unique'   => 'สินค้านี้มีโปรโมชั่นอื่นอยู่แล้ว ไม่สามารถเพิ่มซ้ำได้',
        'promo_discount.required' => 'กรุณากรอกราคาที่ลด',
        'promo_discount.min'      => 'ราคาที่ลดต้องไม่ต่ำกว่า 0 บาท',
        'promo_discount.max'      => "ราคาที่ลดต้องไม่เกินราคาสินค้า ({$productPrice} บาท)", // 👈 ข้อความใหม่
    ];

    $request->validate($rules, $messages);

    Promotion::create($request->all());

    return redirect()->route('promotion.index')->with('success', 'เพิ่มโปรโมชั่นเรียบร้อยแล้ว');
}
  

 public function edit($id)
    {
        // 1. ดึงโปรโมชั่นปัจจุบัน (พร้อมนับ Order)
        $promotion = Promotion::withCount('orders')->findOrFail($id);
        
        // 2. ดึงสินค้าทั้งหมด
        $products = Product::all();
        
        // 3. [เพิ่ม] ดึง ID สินค้าทั้งหมดที่มีโปรโมชั่นอยู่แล้ว (ยกเว้นตัวมันเอง)
        $promotedProductIds = Promotion::where('promo_id', '!=', $id)
                                        ->pluck('pro_id');
        
        // 4. ส่งข้อมูลทั้งหมดไปที่ View
        return view('layouts.promotion.edit', compact(
            'promotion', 
            'products', 
            'promotedProductIds' // 👈 ส่งตัวแปรใหม่ไปด้วย
        ));
    }
    /**
     * อัปเดตข้อมูลโปรโมชั่น
     */
    public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        // (Validation Logic - เหมือนกับ store)
        $productId = $request->input('pro_id');
        $productPrice = 0;
        if ($productId) {
            $product = Product::find($productId);
            if ($product) $productPrice = $product->price;
        }

        $rules = [
            'promo_name'     => 'required|string|max:50',
            'promo_start'    => 'required|date',
            'promo_end'      => 'required|date|after_or_equal:promo_start',
            'pro_id' => [
                'required',
                'integer',
                'exists:product,pro_id',
                // [สำคัญ] 👈 บอกให้ Rule "unique" ไม่ต้องเช็ค ID ของตัวเอง
                Rule::unique('promotion', 'pro_id')->ignore($id, 'promo_id'), 
            ],
            'promo_discount' => [
                'required', 'numeric', 'min:0', "max:{$productPrice}"
            ],
        ];

        $messages = [
            'pro_id.required' => 'กรุณาเลือกสินค้าที่ร่วมรายการ',
            'pro_id.unique'   => 'สินค้านี้มีโปรโมชั่นอื่นอยู่แล้ว ไม่สามารถเพิ่มซ้ำได้',
            'promo_discount.required' => 'กรุณากรอกราคาที่ลด',
            'promo_discount.min'      => 'ราคาที่ลดต้องไม่ต่ำกว่า 0 บาท',
            'promo_discount.max'      => "ราคาที่ลดต้องไม่เกินราคาสินค้า ({$productPrice} บาท)",
        ];

        $validated = $request->validate($rules, $messages);

        $promotion->update($validated); // 👈 ใช้อัปเดตข้อมูล

        return redirect()->route('promotion.index')->with('success', 'แก้ไขโปรโมชั่นเรียบร้อยแล้ว');
    }

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