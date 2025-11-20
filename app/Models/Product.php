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
    public function promotions()
{
    return $this->hasMany(Promotion::class, 'pro_id', 'pro_id');
}
   
}