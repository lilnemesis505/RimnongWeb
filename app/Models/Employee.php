<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee';
    protected $primaryKey = 'em_id';
    protected $keyType = 'int'; // ถ้า em_id เป็น integer
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'em_name',
        'username',
        'password',
        'em_tel',
        'em_email'
    ];

    // สำคัญ: บอก Laravel ว่าใช้ em_id สำหรับ route binding
    public function getRouteKeyName()
    {
        return 'em_id';
    }
}
