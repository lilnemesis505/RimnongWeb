<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // asdasdda
    public function up(): void
{
    Schema::create('employee', function (Blueprint $table) {
        $table->id('em_id');
        $table->string('em_name', 60);
        $table->string('username', 35)->unique();
        $table->string('password'); 
        $table->string('em_tel', 10);
        $table->string('em_email', 100)->unique();
    });
}

public function down(): void
{
    Schema::dropIfExists('employee');
}
};
