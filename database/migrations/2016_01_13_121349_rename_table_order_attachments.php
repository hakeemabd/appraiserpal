<?php

use Illuminate\Database\Migrations\Migration;

class RenameTableOrderAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('order_attachments', 'attachment_order');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('attachment_order', 'order_attachments');

    }
}
