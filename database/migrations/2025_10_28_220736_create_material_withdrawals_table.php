<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * (โค้ดสำหรับสร้างตาราง)
     */
    public function up(): void
    {
        Schema::create('material_withdrawals', function (Blueprint $table) {
            $table->id('withdrawal_id'); // ID ของการเบิก
            $table->unsignedBigInteger('mat_id'); // FK ไปยัง stock_mat
            $table->unsignedBigInteger('admin_id'); // FK ไปยัง admin
            
            $table->integer('withdraw_amount'); // จำนวนที่เบิก
            $table->decimal('calculated_cost', 10, 2); // ราคาที่คำนวณได้
            
            $table->timestamps(); // จะสร้าง created_at (ใช้แทน withdraw_date) และ updated_at

            // สร้าง Foreign Keys
            $table->foreign('mat_id')->references('mat_id')->on('stock_mat')->onDelete('restrict');
            $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     * (โค้ดสำหรับลบตาราง)
     */
    public function down(): void
    {
        Schema::dropIfExists('material_withdrawals');
    }
};