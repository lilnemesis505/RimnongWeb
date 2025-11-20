<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon; // 👈 [เพิ่ม] 1. Import Carbon

class Promotion extends Model
{
    protected $primaryKey = 'promo_id';
    public $timestamps = false;
    protected $table = 'promotion';
    protected $fillable = [
        'promo_name',
        'promo_discount',
        'pro_id',
        'promo_start',
        'promo_end',
    ];
    
    // 👈 [เพิ่ม] 2. เพิ่ม $casts เพื่อให้ Laravel รู้ว่านี่คือ "วันที่"
    // (สำคัญมาก! จะทำให้ $this->promo_start เป็น Object อัตโนมัติ)
    protected $casts = [
        'promo_start' => 'date',
        'promo_end'   => 'date',
    ];
   
    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id','pro_id');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_promotion', 'promo_id', 'order_id');
    }

    // ----------------------------------------------------
    // [เพิ่ม] 3. Accessor สำหรับ "ข้อความสถานะ"
    // (จะถูกเรียกอัตโนมัติเมื่อเราใช้ $promotion->status_text)
    // ----------------------------------------------------
    public function getStatusTextAttribute()
    {
        $today = Carbon::today();
        
        // (เราใช้ $this->promo_start ได้เลยเพราะ $casts)
        if ($today->lt($this->promo_start)) {
            return 'ยังไม่เริ่ม';
        }

        // (ใช้ between() เพื่อเช็คว่าอยู่ "ระหว่าง" หรือไม่)
        if ($today->between($this->promo_start, $this->promo_end)) {
            return 'ใช้งานได้';
        }

        return 'หมดอายุ';
    }

    // ----------------------------------------------------
    // [เพิ่ม] 4. Accessor สำหรับ "สีของสถานะ"
    // (จะถูกเรียกอัตโนมัติเมื่อเราใช้ $promotion->status_class)
    // ----------------------------------------------------
    public function getStatusClassAttribute()
    {
        $today = Carbon::today();
        
        if ($today->lt($this->promo_start)) {
            return 'badge-info'; // สีฟ้า
        }

        if ($today->between($this->promo_start, $this->promo_end)) {
            return 'badge-success'; // สีเขียว
        }

        return 'badge-danger'; // สีแดง
    }
}