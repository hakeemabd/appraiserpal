<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('format', [
                'document',
                'image',
                'other'
            ]);
            $table->enum('type', [
                'data_file_mobile',
                'data_file_manual',
                'photo',
                'comparable',
                'comparable_info',
                'mls',
                'clone',
                'sample',
                'sketch',
                'mc_1004',
                'adj_sheets',
                'report',
                'contact',
                'subject',
            ]);
            $table->string('location');
            $table->string('s3key');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('order_id')->unsigned()->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->text('label')->nullable();
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
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropForeign('attachments_user_id_foreign');
            $table->dropForeign('attachments_order_id_foreign');
        });
        Schema::drop('attachments');
    }
}
