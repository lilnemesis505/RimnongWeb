<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMatTable extends Migration
{
    public function up()
    {
        Schema::create('stock_mat', function (Blueprint $table) {
            $table->bigIncrements('mat_id'); // Primary Key
            $table->string('mat_name', 50);
            $table->unsignedBigInteger('type_id'); // Foreign Key
            $table->timestamp('import_date')->nullable();
            $table->integer('quantity')->default(0);
            $table->timestamp('exp_date')->nullable();
            $table->integer('remain')->default(0);
            $table->decimal('unitcost', 10, 2)->default(0);
            $table->tinyInteger('status')->default(0);

            //เชื่อมกับตาราง protype
            $table->foreign('type_id')
                  ->references('type_id')
                  ->on('protype')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_mat');
    }
}
