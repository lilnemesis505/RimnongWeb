<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_promotion', function (Blueprint $table) {
            $table->foreignId('order_id')->constrained('order', 'order_id')->onDelete('cascade');
            $table->foreignId('promo_id')->constrained('promotion', 'promo_id')->onDelete('cascade');
            $table->primary(['order_id', 'promo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_promotion');
    }
};
