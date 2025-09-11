<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->id('pro_id'); // Primary key Auto Increment
            $table->unsignedBigInteger('type_id'); 
            $table->string('pro_name', 50); 
            $table->decimal('price', 10, 2); 

            // เพิ่ม foreign key constraint
            $table->foreign('type_id')
                  ->references('type_id') // หรือ 'id' ถ้าใช้ Laravel default
                  ->on('protype')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
