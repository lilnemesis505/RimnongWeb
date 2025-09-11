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
        'promo_start',
        'promo_end',
    ];
}
