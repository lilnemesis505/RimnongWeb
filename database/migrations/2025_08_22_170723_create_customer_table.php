<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerTable extends Migration
{
    public function up()
    {
        // asdasd
        Schema::create('customer', function (Blueprint $table) {
            $table->bigIncrements('cus_id'); // PK, auto-increment

            $table->string('fullname', 60);   // ชื่อ-สกุล
            $table->string('username', 30);   // Username
            $table->string('password');   // รหัสผ่าน
            $table->string('cus_tel', 10);    // เบอร์โทร
            $table->string('email', 50);      // Email
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer');
    }


};
