<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddCustomerWorkerFieldsUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('mobile_phone');
            $table->string('work_phone');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('state');
            $table->string('zip');
            $table->string('license_number');
            $table->string('flat_fee');
            $table->string('first_turnaround');
            $table->string('next_turnaround');
            $table->boolean('auto_comments');
            $table->boolean('auto_orders');
            $table->string('paypal_email');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('routing_number');
            $table->boolean('confirmed');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'mobile_phone',
                'work_phone',
                'address_line_1',
                'address_line_2',
                'state',
                'zip',
                'license_number',
                'flat_fee',
                'first_turnaround',
                'next_turnaround',
                'auto_comments',
                'auto_orders',
                'paypal_email',
                'bank_name',
                'account_number',
                'routing_number',
                'confirmed'
            ]);
        });
    }
}
