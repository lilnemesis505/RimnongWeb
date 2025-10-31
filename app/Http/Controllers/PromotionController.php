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
    $productId = $request->input('pro_id');
    $productPrice = 0; 
    if ($productId) {
        $product = Product::find($productId);
        if ($product) $productPrice = $product->price;
    }

    $rules = [
        'promo_name'     => 'required|string|max:50',
        'promo_start'    => 'required|date',
        // [แก้ไข] 1. (ข้อ 2) กฎนี้ถูกต้องแล้ว (สิ้นสุด ห้ามอยู่ก่อนเริ่ม)
        'promo_end'      => 'required|date|after_or_equal:promo_start', 
        
        'pro_id' => [
            'required',
            'integer',
            'exists:product,pro_id',
            // [แก้ไข] 2. (ข้อ 1) ลบ Rule::unique() ออก และใช้ Custom Rule นี้แทน
            function ($attribute, $value, $fail) use ($request) {
                $newStart = $request->input('promo_start');
                $newEnd = $request->input('promo_end');

                // ค้นหาโปรโมชั่น "ใดๆ" ของสินค้านี้ ที่มีวันที่ทับซ้อนกัน
                $overlappingPromo = Promotion::where('pro_id', $value)
                    ->where(function ($query) use ($newStart, $newEnd) {
                        // 1. วันที่เริ่ม/สิ้นสุดใหม่ "คร่อม" วันที่เดิม
                        $query->whereBetween('promo_start', [$newStart, $newEnd])
                        // 2. วันที่เริ่ม/สิ้นสุดเดิม "คร่อม" วันที่ใหม่
                              ->orWhereBetween('promo_end', [$newStart, $newEnd])
                        // 3. วันที่ใหม่ "อยู่ภายใน" วันที่เดิม
                              ->orWhere(function($q) use ($newStart, $newEnd) {
                                  $q->where('promo_start', '<=', $newStart)
                                    ->where('promo_end', '>=', $newEnd);
                              });
                    })
                    ->first(); // 👈 ค้นหาแค่ 1 รายการก็พอ

                if ($overlappingPromo) {
                    $fail("สินค้านี้มีโปรโมชั่นในช่วงวันที่ ({$overlappingPromo->promo_start} ถึง {$overlappingPromo->promo_end}) ทับซ้อนกันอยู่แล้ว");
                }
            }
        ],
        'promo_discount' => [
            'required', 'numeric', 'min:0', "max:{$productPrice}"
        ],
    ];

    $messages = [
        'pro_id.required' => 'กรุณาเลือกสินค้าที่ร่วมรายการ',
        'pro_id.exists'   => 'สินค้าที่เลือกไม่มีอยู่ในระบบ',
        // [แก้ไข] 3. ลบข้อความ .unique ออก
        'promo_discount.required' => 'กรุณากรอกราคาที่ลด',
        'promo_discount.min'      => 'ราคาที่ลดต้องไม่ต่ำกว่า 0 บาท',
        'promo_discount.max'      => "ราคาที่ลดต้องไม่เกินราคาสินค้า ({$productPrice} บาท)",
    ];

    $request->validate($rules, $messages);
    Promotion::create($request->all());
    return redirect()->route('promotion.index')->with('success', 'เพิ่มโปรโมชั่นเรียบร้อยแล้ว');
}
  
   public function edit($id)
    {
        // (ฟังก์ชัน edit ... Giongเดิม)
        $promotion = Promotion::withCount('orders')->findOrFail($id);
        $products = Product::all();
        $promotedProductIds = Promotion::where('promo_id', '!=', $id)
                                        ->pluck('pro_id');
        
        return view('layouts.promotion.edit', compact(
            'promotion', 
            'products', 
            'promotedProductIds' 
        ));
    }
    
   public function update(Request $request, $id)
    {
        $promotion = Promotion::findOrFail($id);

        $productId = $request->input('pro_id');
        $productPrice = 0;
        if ($productId) {
            $product = Product::find($productId);
            if ($product) $productPrice = $product->price;
        }

        $rules = [
            'promo_name'     => 'required|string|max:50',
            'promo_start'    => 'required|date',
            // [แก้ไข] 4. (ข้อ 2) กฎนี้ถูกต้อง
            'promo_end'      => 'required|date|after_or_equal:promo_start',
            'pro_id' => [
                'required',
                'integer',
                'exists:product,pro_id',
                // [แก้ไข] 5. (ข้อ 1) ใช้ Custom Rule เดียวกับ store แต่ "ยกเว้น" ID ตัวเอง
                function ($attribute, $value, $fail) use ($request, $id) {
                    $newStart = $request->input('promo_start');
                    $newEnd = $request->input('promo_end');

                    $overlappingPromo = Promotion::where('pro_id', $value)
                        ->where('promo_id', '!=', $id) // 👈 (ยกเว้น ID ของโปรโมชั่นนี้)
                        ->where(function ($query) use ($newStart, $newEnd) {
                            $query->whereBetween('promo_start', [$newStart, $newEnd])
                                  ->orWhereBetween('promo_end', [$newStart, $newEnd])
                                  ->orWhere(function($q) use ($newStart, $newEnd) {
                                      $q->where('promo_start', '<=', $newStart)
                                        ->where('promo_end', '>=', $newEnd);
                                  });
                        })
                        ->first();

                    if ($overlappingPromo) {
                        $fail("สินค้านี้มีโปรโมชั่นในช่วงวันที่ ({$overlappingPromo->promo_start} ถึง {$overlappingPromo->promo_end}) ทับซ้อนกันอยู่แล้ว");
                    }
                }
            ],
            'promo_discount' => [
                'required', 'numeric', 'min:0', "max:{$productPrice}"
            ],
        ];

        $messages = [
            'pro_id.required' => 'กรุณาเลือกสินค้าที่ร่วมรายการ',
            'promo_discount.required' => 'กรุณากรอกราคาที่ลด',
            'promo_discount.min'      => 'ราคาที่ลดต้องไม่ต่ำกว่า 0 บาท',
            'promo_discount.max'      => "ราคาที่ลดต้องไม่เกินราคาสินค้า ({$productPrice} บาท)",
        ];

        $validated = $request->validate($rules, $messages);
        
        if ($promotion->orders()->count() > 0) {
            $validated['promo_discount'] = $promotion->promo_discount;
            $validated['pro_id'] = $promotion->pro_id;
        }

        $promotion->update($validated); 
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