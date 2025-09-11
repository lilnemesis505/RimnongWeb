<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Protype;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $primaryKey = 'pro_id';
    public $timestamps = false;

    protected $fillable = [
        'type_id',
        'pro_name',
        'price',
        
    ];

    public function type()
    {
        return $this->belongsTo(Protype::class, 'type_id');
    }
   public function getImagePathAttribute()
{
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    foreach ($extensions as $ext) {
        $path = "storage/products/{$this->pro_id}.{$ext}";
        if (file_exists(public_path($path))) {
            return asset($path) . '?v=' . time(); // 🔥 ป้องกันแคช
        }
    }
    return asset('images/no-image.png');
}


}