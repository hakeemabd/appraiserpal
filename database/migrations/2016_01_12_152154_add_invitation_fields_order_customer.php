<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddInvitationFieldsOrderCustomer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('auto_invite')->default(false);
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('auto_invite')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('auto_invite');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('auto_invite');
        });
    }
}
