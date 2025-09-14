<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Receipt;

class Order extends Model
{
    use HasFactory;

    // ✅ เพิ่มส่วนนี้เข้าไปทั้งหมด
    protected $primaryKey = 'order_id'; // บอก Laravel ว่า Primary Key ของตารางนี้คือ 'order_id'
    public $timestamps = false; // บอก Laravel ว่าตารางนี้ไม่มีคอลัมน์ created_at, updated_at
    protected $table = 'order';
    protected $fillable = [
        'cus_id',
        'order_date',
        'em_id',
        'promo_id',
        'price_total',
        'receive_date',
        'remarks',
        'grab_date',
        'slips_url',
        'slips_id'
    ];

    // --- ส่วนของ Relationships (ถ้ามีอยู่แล้วก็ไม่ต้องเพิ่ม) ---
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cus_id', 'cus_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'em_id', 'em_id');
    }

    public function promotion()
    {
        return $this->belongsTo(Promotion::class, 'promo_id', 'promo_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }
    public function receipt()
    {
     return $this->hasOne(Receipt::class, 'order_id', 'order_id');
    }

}