<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    protected $table = 'order_detail';
    protected $primaryKey = 'order_id';
    public $timestamps = false;
    protected $fillable = [
        'order_id', 'pro_id', 'amount', 'price_list', 'pay_total'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'pro_id');
    }
}