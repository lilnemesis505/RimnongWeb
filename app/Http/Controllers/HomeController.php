<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Order;
use App\Models\StockMat;
use App\Models\Promotion;
use Carbon\Carbon;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('admin.auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ดึงข้อมูลสรุป
        $customerCount = Customer::count();
        $employeeCount = Employee::count();
        $totalSales = Order::whereNotNull('em_id')->sum('price_total');
        $productCount = Product::count(); // เพิ่มบรรทัดนี้เพื่อนับจำนวนสินค้าทั้งหมด
        
        // ดึงวัตถุดิบที่ใกล้หมดอายุ (หมดภายใน 7 วัน)
        $today = Carbon::today();
        $sevenDaysFromNow = $today->copy()->addDays(7);
        $expiringStock = StockMat::where('exp_date', '>=', $today)
            ->where('exp_date', '<=', $sevenDaysFromNow)
            ->where('remain', '<', 3) // ✅ กรองเฉพาะที่เหลือน้อยกว่า 3
            ->get();

        // ดึงโปรโมชั่นที่ยังใช้งานได้และคำนวณวันหมดอายุ
        $activePromotions = Promotion::where('promo_start', '<=', $today)
            ->where('promo_end', '>=', $today)
            ->get()
            ->map(function ($promo) use ($today) {
                $promo->days_left = Carbon::parse($promo->promo_end)->diffInDays($today);
                return $promo;
            });
        
        // ส่งข้อมูลไปยังหน้า welcome.blade.php
        return view('welcome', compact(
            'customerCount', 
            'employeeCount', 
            'totalSales', 
            'expiringStock', 
            'activePromotions',
            'productCount' // ส่งตัวแปรจำนวนสินค้าไปยัง View
        ));
    }
}
