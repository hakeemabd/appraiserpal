<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_attachments', function (Blueprint $table) {
            $table->integer('order_id')->unsigned();
            $table->integer('attachment_id')->unsigned();
            $table->primary(['order_id', 'attachment_id']);
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('attachment_id')->references('id')->on('attachments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_attachments', function (Blueprint $table) {
            $table->dropForeign('order_attachments_attachment_id_foreign');
            $table->dropForeign('order_attachments_order_id_foreign');
        });
        Schema::drop('order_attachments');
    }
}
