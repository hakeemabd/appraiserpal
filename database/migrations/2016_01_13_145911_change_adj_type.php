<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ChangeAdjType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('adjustment_type');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('adjustment_type', [
                'none',
                'add regression',
                'own'
            ])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('adjustment_type');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('adjustment_type')->nullable();
        });
    }
}
