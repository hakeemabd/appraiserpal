<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class UpdateAttachmentsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('attachments', function (Blueprint $table) {
            $table->enum('type', [
                'data_file_mobile',
                'data_file_manual',
                'photo',
                'comparables',
                'comparable_info',
                'mls',
                'clone',
                'sample',
                'sketch',
                'mc_1004',
                'adj_sheets',
                'report',
                'contract',
                'subject',
            ]);
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
            $table->dropColumn('type');
        });
        Schema::table('attachments', function (Blueprint $table) {
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
                'contract',
                'subject',
            ]);
        });
    }
}