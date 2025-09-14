<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
   
    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id','pro_id');
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_promotion', 'promo_id', 'order_id');
    }
}
