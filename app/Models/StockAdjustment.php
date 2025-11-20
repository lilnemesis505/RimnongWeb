<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;
    
    protected $table = 'stock_adjustments';
    protected $primaryKey = 'adjustment_id';
    
    // [แก้ไข] 1. ปิด Timestamps (created_at, updated_at)
    public $timestamps = false; 

    /**
     * [แก้ไข] 2. อัปเดต fillable ให้ตรงกับ Migration ใหม่
     */
    protected $fillable = [
        'stock_mat_id',
        'admin_id',
        'amount',       // 👈 เหลือแค่ amount
        'adjust_date',  // 👈 ใช้ adjust_date
    ];

    /**
     * [แก้ไข] 3. เปลี่ยน casts ให้เป็น adjust_date
     */
    protected $casts = [
        'adjust_date' => 'datetime',
    ];

    // (Relationships เหมือนเดิม)
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    public function stockMat()
    {
        return $this->belongsTo(StockMat::class, 'stock_mat_id', 'mat_id');
    }
}