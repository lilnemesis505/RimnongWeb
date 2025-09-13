<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;
    
    protected $table = 'receipt';
    protected $primaryKey = 're_id';
    public $timestamps = false;
    
    protected $fillable = [
        'order_id',
        're_date',
        'price_total',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}