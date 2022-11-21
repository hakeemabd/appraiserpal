<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddPaymentFieldsToOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('paid')->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('archived_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
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
            $table->dropColumn(['paid', 'paid_at', 'delivered_at', 'archived_at', 'canceled_at']);
        });
    }
}
