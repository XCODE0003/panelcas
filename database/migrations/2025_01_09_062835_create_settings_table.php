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
        Schema::create('settings_workers', function (Blueprint $table) {
            $table->id();
            $table->integer('win_chance')->default(50);
            $table->integer('minimal_deposit')->default(100);
            $table->integer('min_withdraw_worker')->default(100);
            $table->integer('percent_profit_worker')->default(75);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
