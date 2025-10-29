<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id('adjustment_id');
            $table->unsignedBigInteger('stock_mat_id');
            $table->unsignedBigInteger('admin_id');
            
            $table->string('reason_type', 50); 
            $table->integer('change_amount');  
            $table->integer('new_remain');     
            
            $table->timestamps(); 

            // [FIX 6] Change 'stock_mats' to 'stock_mat'
            $table->foreign('stock_mat_id')
                  ->references('mat_id')
                  ->on('stock_mat') // 👈 This is the fix
                  ->onDelete('restrict'); 
                  
            $table->foreign('admin_id')->references('admin_id')->on('admin')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};