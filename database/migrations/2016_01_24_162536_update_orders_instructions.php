<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrdersInstructions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('specific_instructions', 'standard_instructions', 'title');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('specific_instructions', 65000)->nullable();
            $table->string('standard_instructions', 65000)->nullable();
            $table->string('title', 100)->nullable();
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
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('specific_instructions', 'standard_instructions', 'title');
            });
            Schema::table('orders', function (Blueprint $table) {
                $table->string('specific_instructions')->nullable();
                $table->text('standard_instructions')->nullable();
                $table->string('title')->nullable();
            });
        });
    }
}
