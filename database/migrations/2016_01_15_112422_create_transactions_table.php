<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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
            $table->boolean('paid_by')->default(0);
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
    }
}
