<?php
namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Product; // <-- 1. Import Model Product
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        // 4. กำหนดกฎและข้อความแจ้งเตือน
        $rules = [
            'promo_name'     => 'required|string|max:50',
            'promo_discount' => 'required|numeric',
            'promo_start'    => 'required|date',
            'promo_end'      => 'required|date|after_or_equal:promo_start',
            'pro_id'         => 'required|integer|exists:product,pro_id', // <-- เพิ่มกฎสำหรับ pro_id
        ];

        $messages = [
            'pro_id.required' => 'กรุณาเลือกสินค้าที่ร่วมรายการ',
            'pro_id.exists'   => 'สินค้าที่เลือกไม่มีอยู่ในระบบ',
        ];

        $request->validate($rules, $messages);

        Promotion::create($request->all());
        return redirect()->back()->with('success', 'เพิ่มโปรโมชั่นเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        Promotion::destroy($id);
        return redirect()->back()->with('success', 'ลบโปรโมชั่นเรียบร้อยแล้ว');
    }

    // ... (ฟังก์ชัน check ไม่มีการเปลี่ยนแปลง)
    public function check(Request $request)
    {
        // ... โค้ดเดิม ...
    }
}