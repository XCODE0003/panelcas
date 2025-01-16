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
        Schema::create('withdraw_workers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('address');
            $table->enum('status', ['pending', 'completed', 'rejected'])->default('pending');
            $table->string('amount');
            $table->string('currency')->default('USDT TRC20');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdraw_workers');
    }
};
