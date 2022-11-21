<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->double('amount');
            $table->enum('status', [
                'new',
                'paid',
                'delayed',
                'canceled',
                'overdue',
            ]);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delayed_at')->nullable();
            $table->timestamp('delayed_until')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('overdue_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('payments');
    }
}
