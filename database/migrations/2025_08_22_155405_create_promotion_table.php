<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('promotion', function (Blueprint $table) {
        $table->bigIncrements('promo_id');
        $table->string('promo_name', 50);
        $table->decimal('promo_discount', 10, 2);
        $table->date('promo_start');   
        $table->date('promo_end');     

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion');
    }
};
