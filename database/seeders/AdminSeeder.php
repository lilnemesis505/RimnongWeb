<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin; // แก้ไขจาก App\Models\User เป็น App\Models\Admin

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ใช้ Model Admin เพื่อสร้างข้อมูลผู้ดูแลระบบ
        Admin::create([
            'fullname' => 'Admin Tester', // ใช้ชื่อคอลัมน์จาก Model Admin
            'username' => 'admin', // เพิ่ม username
            'email' => 'admin@example.com',
            'password' => Hash::make('123456'),
            'admin_tel' => '0123456789', // เพิ่ม admin_tel
        ]);
    }
}
// www
