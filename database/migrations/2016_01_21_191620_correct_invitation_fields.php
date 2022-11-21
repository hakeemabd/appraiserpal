<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CorrectInvitationFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dateTime('sent_at')->nullable()->change();
            $table->dateTime('accepted_at')->nullable()->change();
            $table->dateTime('rejected_at')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invitations', function (Blueprint $table) {
            $table->dateTime('sent_at')->change();
            $table->dateTime('accepted_at')->change();
            $table->dateTime('rejected_at')->change();
        });
    }
}
