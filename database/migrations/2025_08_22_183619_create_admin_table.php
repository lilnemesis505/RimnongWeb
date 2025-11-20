<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTable extends Migration
{
    // asdasdasd
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id('admin_id'); // รหัสผู้ดูแลระบบ (auto increment)
            $table->string('fullname', 60); // ชื่อ-สกุล
            $table->string('username', 35)->unique(); // ชื่อผู้ใช้
            $table->string('password'); // รหัสผ่าน
            $table->string('email', 50)->unique(); // อีเมล
            $table->string('admin_tel', 10); // เบอร์โทร

        });
    }

    public function down()
    {
        Schema::dropIfExists('admin');
    }
};
