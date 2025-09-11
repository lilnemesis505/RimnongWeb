<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt', function (Blueprint $table) {
            $table->bigIncrements('re_id'); // รหัสใบเสร็จ (PK)
            $table->unsignedBigInteger('order_id'); // รหัสการสั่งซื้อ (FK → orders)
            $table->timestamp('re_date')->useCurrent(); // วันที่ออกใบเสร็จ
            $table->decimal('price_total', 10, 2); // ราคารวมทั้งหมด

            $table->timestamps();

            // Foreign key constraint
            $table->foreign('order_id')->references('order_id')->on('order')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt');
    }
};
