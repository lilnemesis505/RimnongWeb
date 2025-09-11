<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    protected $primaryKey = 'cus_id';
    public $timestamps = false;
    protected $fillable = [
        'fullname', 'username', 'password', 'cus_tel', 'email'
    ];
}