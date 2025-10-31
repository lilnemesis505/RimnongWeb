<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'cus_id';
    public $timestamps = false;
    protected $fillable = [
        'fullname', 'username', 'password', 'cus_tel', 'email'
    ];
    public function orders()
    {

        return $this->hasMany(Order::class, 'cus_id', 'cus_id');
    }
    public function receipts()
    {
        return $this->hasManyThrough(
            Receipt::class, // 👈 Model ปลายทาง (ที่เราต้องการ)
            Order::class,   // 👈 Model ตรงกลาง (ที่เชื่อมอยู่)
            'cus_id',     // Foreign key บนตาราง Order (ที่เชื่อมกับ Customer)
            'order_id',   // Foreign key บนตาราง Receipt (ที่เชื่อมกับ Order)
            'cus_id',     // Local key บนตาราง Customer (ตารางนี้)
            'order_id'    // Local key บนตาราง Order (ตารางกลาง)
        );
    }
}