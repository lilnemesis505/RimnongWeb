<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Protype;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $primaryKey = 'pro_id';
    public $timestamps = false;

    protected $fillable = [
        'type_id',
        'pro_name',
        'price',
        'image',
        'image_id', // 🔥 เพิ่ม field 'image' เข้ามาใน fillable
    ];

    public function type()
    {
        return $this->belongsTo(Protype::class, 'type_id');
    }
   
    // 🔥 ลบ method getImagePathAttribute() ทิ้ง
    // เพราะเราจะใช้ field 'image' ที่เก็บ URL จาก ImageKit โดยตรง
}