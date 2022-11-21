<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddColumnToAttachment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->integer('software_id')->unsigned()->nullable();
            $table->foreign('software_id')->references('id')->on('softwares');
            $table->integer('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types');
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
            $table->dropForeign('attachments_software_id_foreign');
            $table->dropForeign('attachments_order_type_id_foreign');
            $table->dropColumn('software_id');
            $table->dropColumn('order_type_id');
        });
    }
}
