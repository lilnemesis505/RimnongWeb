<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_detail', function (Blueprint $table) { // เปลี่ยนชื่อตารางเป็น 'order_details'
            $table->unsignedBigInteger('order_id'); // PK, FK -> orders
            $table->unsignedBigInteger('pro_id');  // PK, FK -> products

            $table->integer('amount');      // จำนวนสินค้า
            $table->decimal('price_list', 10, 2); // ราคาต่อหน่วย
            $table->decimal('pay_total', 10, 2);  // ราคารวมต่อรายการ

            // Composite Primary Key (order_id + pro_id)
            $table->primary(['order_id', 'pro_id']);

            // Foreign Keys
            $table->foreign('order_id')->references('order_id')->on('order')->onDelete('cascade'); // แก้ไขเป็น 'orders'
            $table->foreign('pro_id')->references('pro_id')->on('product')->onDelete('cascade'); // แก้ไขเป็น 'products'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_detail');
    }
}