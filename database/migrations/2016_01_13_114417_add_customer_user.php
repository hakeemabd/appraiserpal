<?php

use Illuminate\Database\Migrations\Migration;

class AddCustomerUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Sentinel::registerAndActivate([
            'email' => 'ernesto+customer@tmake.mx',
            'password' => 'customer',
            'first_name' => 'Ernesto',
            'last_name' => 'Customer',
            'mobile_phone' => '12345'
        ]);

        $user = Sentinel::findByCredentials([
            'email' => 'ernesto+customer@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('customer');
        $role->users()->attach($user);

        Sentinel::registerAndActivate([
            'email' => 'jzazueta+customer@tmake.mx',
            'password' => 'customer',
            'first_name' => 'Jonatthan',
            'last_name' => 'Customer',
            'mobile_phone' => '12345'
        ]);

        $user = Sentinel::findByCredentials([
            'email' => 'jzazueta+customer@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('customer');
        $role->users()->attach($user);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $user = Sentinel::findByCredentials([
            'email' => 'ernesto+customer@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('customer');
        $role->users()->detach($user);

        $user = Sentinel::findByCredentials([
            'email' => 'jzazueta+customer@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('customer');
        $role->users()->detach($user);

//        $user->delete();
    }
}
