<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RemoveWorkerGroupFieldsFromUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['flat_fee', 'first_turnaround', 'next_turnaround', 'auto_orders']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('flat_fee');
            $table->string('first_turnaround');
            $table->string('next_turnaround');
            $table->boolean('auto_orders');
        });
    }
}
