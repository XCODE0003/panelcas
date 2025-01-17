<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->integer('min_deposit_activation')->default(100);
        });
    }

    public function down()
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn('min_deposit_activation');
        });
    }
}; 