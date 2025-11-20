<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Protype extends Model
{
    protected $table = 'protype';
    protected $primaryKey = 'type_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = ['type_name'];
}
