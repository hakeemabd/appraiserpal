<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddOrderAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_assignments', function (Blueprint $table) {
            $table->integer('order_id')->unsigned();
            $table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('status');
            $table->unsignedBigInteger('worklog_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('worklog_id')->references('id')->on('worklogs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('order_assignments');
    }
}
