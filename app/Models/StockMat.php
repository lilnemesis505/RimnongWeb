<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMat extends Model
{
    protected $table = 'stock_mat';
    protected $primaryKey = 'mat_id'; 
    public $timestamps = false; 

    protected $fillable = [
        'mat_name',
        'type_id',
        'import_date',
        'quantity',
        'exp_date',
        'remain',
        'unitcost',
        'status',
        'image',      
        'image_id',
    ];

    public function type()
    {
        return $this->belongsTo(Protype::class, 'type_id');
    }

}