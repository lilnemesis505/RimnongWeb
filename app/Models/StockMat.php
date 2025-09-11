<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMat extends Model
{
    protected $table = 'stock_mat';
    protected $primaryKey = 'mat_id'; 
    protected $fillable = [
        'mat_name',
        'type_id',
        'import_date',
        'quantity',
        'exp_date',
        'remain',
        'unitcost',
        'status',
    ];
    public $timestamps = false; 

    public function type()
{
    return $this->belongsTo(Protype::class, 'type_id');
}

}
