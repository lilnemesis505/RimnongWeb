<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasFactory;

    /**
     * ชื่อตารางที่โมเดลนี้เชื่อมโยง
     *
     * @var string
     */
    protected $table = 'stock_adjustments';

    /**
     * Primary Key ของตาราง
     *
     * @var string
     */
    protected $primaryKey = 'adjustment_id';

    /**
     * ระบุว่าโมเดลควรมี timestamps (created_at, updated_at) หรือไม่
     * (ตั้งเป็น true เพราะเราต้องการ 'created_at' เพื่อดูว่าปรับยอดเมื่อไหร่)
     *
     * @var bool
     */
     public $timestamps = false;
    
    // (หมายเหตุ: updated_at จะถูกสร้างด้วย แต่เราจะไม่ได้ใช้มันเป็นหลัก)

    /**
     * รายการฟิลด์ที่อนุญาตให้ Mass Assignable
     *
     * @var array
     */
    protected $fillable = [
        'stock_mat_id',
        'admin_id',
        'reason_type',
        'change_amount',
        'new_remain',
        'adjust_date'
    ];

    /**
     * สร้าง Relationship กลับไปยัง Admin (ผู้ที่ปรับยอด)
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }

    /**
     * สร้าง Relationship กลับไปยัง StockMat (สินค้าที่ถูกปรับ)
     */
    public function stockMat()
    {
        return $this->belongsTo(StockMat::class, 'stock_mat_id', 'mat_id');
    }
}