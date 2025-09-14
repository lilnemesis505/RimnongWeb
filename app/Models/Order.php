<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'order';
    protected $primaryKey = 'order_id';
    public $timestamps = false;

    protected $fillable = [
        'cus_id',
        'order_date',
        'receive_date',
        'em_id',
        'price_total',
        'remarks',
        'grab_date',
        'slips_url',
        'slips_id',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cus_id', 'cus_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'em_id', 'em_id');
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class, 'order_id', 'order_id');
    }

    /**
     * The promotions that belong to the order.
     * Defines the many-to-many relationship.
     */
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'order_promotion', 'order_id', 'promo_id');
    }
}