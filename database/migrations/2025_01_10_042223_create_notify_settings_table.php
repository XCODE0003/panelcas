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
        Schema::create('notify_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->boolean('notify_new_visit')->default(false);
            $table->boolean('notify_activate_promo')->default(false);
            $table->boolean('notify_new_payment')->default(false);
            $table->boolean('notify_new_order')->default(false);
            $table->string('bot_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notify_settings');
    }
};
