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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('tg_username');
            $table->string('tg_id')->nullable();
            $table->string('balance')->default(0);
            $table->boolean('is_ban')->default(false);
            $table->boolean('is_support')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->boolean('notify')->default(false);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
