<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->id('order_id');
            $table->unsignedBigInteger('cus_id');
            $table->dateTime('order_date');
            $table->dateTime('receive_date')->nullable();
            $table->unsignedBigInteger('em_id')->nullable();
            $table->unsignedBigInteger('promo_id')->nullable();
            $table->decimal('price_total', 10, 2);
            $table->string('remarks', 255)->nullable();
            $table->string('slips_url', 255)->nullable();
            $table->string('slips_id', 255)->nullable();

            // Foreign Keys
            $table->foreign('cus_id')->references('cus_id')->on('customer')->onDelete('cascade');
            $table->foreign('em_id')->references('em_id')->on('employee')->onDelete('cascade');
            $table->foreign('promo_id')->references('promo_id')->on('promotion')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order');
    }
};
