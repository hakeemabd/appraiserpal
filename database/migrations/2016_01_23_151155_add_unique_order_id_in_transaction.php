<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueOrderIdInTransaction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::drop('transactions');
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned()->unique();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->double('amount');
            $table->string('payment_id')->nullable();
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
        Schema::drop('transactions');
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->double('amount');
            $table->enum('status', [
                'new',
                'paid',
                'delayed',
                'canceled',
                'overdue',
            ]);
            $table->string('payment_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('delayed_at')->nullable();
            $table->timestamp('delayed_until')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('overdue_at')->nullable();
            $table->timestamps();
        });
    }
}
