<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable; // (ต้อง use ด้านบน)

class Admin extends Authenticatable
{
    // ✅ [เพิ่ม] เพิ่ม use Notifiable trait
    use Notifiable;

    protected $table = 'admin';
    protected $primaryKey = 'admin_id'; // (ถูกต้องตาม DB ล่าสุดของคุณ)
    public $timestamps = false;

    protected $fillable = [
        'fullname',
        'username',
        'password', // (ต้องมีเพื่อให้ Hash::make ทำงาน)
        'email',
        'admin_tel'
    ];

    // ✅ [เพิ่ม] ซ่อนรหัสผ่านเมื่อแปลงเป็น Array/JSON
    protected $hidden = [
        'password',
    ];

    // (คุณอาจจะเพิ่ม $casts ที่นี่ในอนาคต ถ้าต้องการ)
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];
}