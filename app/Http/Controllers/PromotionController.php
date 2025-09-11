<?php
namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

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
}
