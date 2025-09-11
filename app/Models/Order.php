<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'order';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'total_amount',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

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
}