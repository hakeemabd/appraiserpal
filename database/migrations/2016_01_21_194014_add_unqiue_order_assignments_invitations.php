<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddUnqiueOrderAssignmentsInvitations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_assignments', function (Blueprint $table) {
            $table->unique(['order_id', 'group_id']);
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->unique(['order_id', 'group_id', 'user_id', 'rejected_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_assignments', function (Blueprint $table) {
            $table->dropForeign('order_assignments_order_id_foreign');
            $table->dropForeign('order_assignments_group_id_foreign');
            $table->dropUnique('order_assignments_order_id_group_id_unique');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('order_id')->references('id')->on('orders');
        });
        Schema::table('invitations', function (Blueprint $table) {
            $table->dropForeign('invitations_order_id_foreign');
            $table->dropForeign('invitations_user_id_foreign');
            $table->dropForeign('invitations_group_id_foreign');
            $table->dropUnique('invitations_order_id_group_id_user_id_rejected_at_unique');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('group_id')->references('id')->on('groups');
        });
    }
}
