<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameOrderType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('order_types', 'report_types');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_order_type_id_foreign');
            $table->dropColumn('order_type_id');
            $table->integer('report_type_id')->unsigned()->nullable();
            $table->foreign('report_type_id')->references('id')->on('report_types');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign('attachments_order_type_id_foreign');
            $table->dropColumn('order_type_id');
            $table->integer('report_type_id')->unsigned()->nullable();
            $table->foreign('report_type_id')->references('id')->on('report_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('report_types', 'order_types');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_report_type_id_foreign');
            $table->dropColumn('report_type_id');
            $table->integer('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types');
        });

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign('attachments_report_type_id_foreign');
            $table->dropColumn('report_type_id');
            $table->integer('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types');
        });
    }
}
