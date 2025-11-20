<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialWithdrawal extends Model
{
    use HasFactory;

    protected $table = 'material_withdrawals';
    protected $primaryKey = 'withdrawal_id';

    // ปิด timestamps (created_at, updated_at) เพราะเราใช้ withdraw_date
    public $timestamps = false;

    protected $fillable = [
        'mat_id',
        'admin_id',
        'withdraw_amount',
        'calculated_cost',
        'withdraw_date', // อนุญาตให้ใส่ withdraw_date ตอน create (เผื่อต้องการ)
    ];

    /**
     * กำหนดให้ withdraw_date เป็น Carbon instance โดยอัตโนมัติ
     */
    protected $casts = [
        'withdraw_date' => 'datetime',
    ];

    // Relationship ไปยัง StockMat
    public function stockMaterial()
    {
        // ใช้ชื่อ Model StockMat ที่ถูกต้อง (ถ้าชื่อไฟล์คือ StockMat.php)
        return $this->belongsTo(StockMat::class, 'mat_id', 'mat_id');
    }

    // Relationship ไปยัง Admin
    public function admin()
    {
        // ใช้ชื่อ Model Admin ที่ถูกต้อง และ Primary Key ที่ถูกต้อง ('admin_id')
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
}