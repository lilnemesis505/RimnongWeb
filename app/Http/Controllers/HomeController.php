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
        // คำนวณยอดขายจากตารางใบเสร็จ (Receipts) ซึ่งจะแม่นยำกว่า
        $totalSales = Receipt::sum('price_total');

        $today = Carbon::today();

        // === 2. ดึงวัตถุดิบที่ใกล้หมดอายุ (หมดอายุใน 7 วันข้างหน้า และยังมีของเหลือ) ===
        $sevenDaysFromNow = $today->copy()->addDays(15);
        $expiringStock = StockMat::where('remain', '>', 0) // เงื่อนไข: ต้องมีของเหลือมากกว่า 0
            ->whereNotNull('exp_date')
            ->whereBetween('exp_date', [$today, $sevenDaysFromNow]) // ใช้ whereBetween เพื่อความชัดเจน
            ->orderBy('exp_date', 'asc')
            ->get()
            ->map(function ($stock) use ($today) {
                // คำนวณวันที่เหลือใน Controller เพื่อให้ View แสดงผลได้เลย
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
        
        // === 4. ส่งข้อมูลทั้งหมดไปยัง View ===
        return view('welcome', compact(
            'customerCount', 
            'employeeCount', 
            'totalSales', 
            'expiringStock', 
            'activePromotions',
            'productCount'
        ));
    }
}