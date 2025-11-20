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
use App\Models\Receipt;
use App\Models\MaterialWithdrawal;
use App\Models\StockAdjustment;

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
        // === 1. ดึงข้อมูลสรุปสำหรับ Info Boxes ===
        $customerCount = Customer::count();
        $employeeCount = Employee::count();
        $productCount = Product::count();
        $totalSales = Receipt::sum('price_total');
        $stockItemCount = StockMat::count();
        
        // [แก้ไข] 👈 1. สร้างตัวแปร $today
        $today = Carbon::today();

        // (ใช้ re_date)
        $todaySales = Receipt::whereDate('re_date', $today)->sum('price_total');

        // === 2. ดึงวัตถุดิบที่ใกล้หมดอายุ (หมดอายุใน 15 วัน) ===
        $sevenDaysFromNow = $today->copy()->addDays(15);
        $expiringStock = StockMat::where('remain', '>', 0) 
            ->whereNotNull('exp_date')
            ->whereBetween('exp_date', [$today, $sevenDaysFromNow]) 
            ->orderBy('exp_date', 'asc')
            ->get()
            ->map(function ($stock) use ($today) {
                $stock->days_to_expire = Carbon::parse($stock->exp_date)->diffInDays($today);
                return $stock;
            });

        // === 3. ดึงโปรโมชั่นที่ยังใช้งานได้ ===
        $activePromotions = Promotion::where('promo_start', '<=', $today)
            ->where('promo_end', '>=', $today)
            ->get()
            ->map(function ($promo) use ($today) {
                $promo->days_left = Carbon::parse($promo->promo_end)->diffInDays($today);
                return $promo;
            });
            
        // 3.1 ดึง 5 การเบิกล่าสุด (สำหรับแท็บ)
        $latestWithdrawals = MaterialWithdrawal::with(['stockMaterial', 'admin']) 
                                             ->orderBy('withdraw_date', 'desc')
                                             ->limit(5) 
                                             ->get();
        // 3.2 ดึงการปรับยอดล่าสุด (สำหรับแท็บ)
        $latestAdjustments = StockAdjustment::with('admin', 'stockMat')
                                ->orderBy('adjust_date', 'desc') 
                                ->limit(20)
                                ->get();
        
        // [แก้ไข] 3.3 ดึง 5 การขายล่าสุด
        $latestSales = Receipt::with([
                                    'order', 
                                    'order.employee' // 👈 [แก้ไข] เปลี่ยนจาก admin เป็น employee
                                ])
                                ->orderBy('re_date', 'desc') 
                                ->limit(5)
                                ->get();
        
        // === 4. ส่งข้อมูลทั้งหมดไปยัง View ===
        return view('welcome', compact(
            'customerCount', 
            'employeeCount', 
            'totalSales', 
            'expiringStock', 
            'activePromotions',
            'productCount',
            'stockItemCount',
            'latestWithdrawals',
            'latestAdjustments',
            'today', // 👈 [แก้ไข] 4. ส่ง $today ไปที่ View
            'todaySales', // 👈 [แก้ไข] 5. ส่ง $todaySales ไปที่ View
            'latestSales' // 👈 [แก้ไข] 6. ส่ง $latestSales ไปที่ View
        ));
    }
}