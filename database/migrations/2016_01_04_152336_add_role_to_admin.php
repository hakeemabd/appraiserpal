<?php

use Illuminate\Database\Migrations\Migration;

class AddRoleToAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]);
        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Worker',
            'slug' => 'worker',
        ]);
        Sentinel::getRoleRepository()->createModel()->create([
            'name' => 'Customer',
            'slug' => 'customer',
        ]);
        $user = Sentinel::findByCredentials([
            'email' => 'ernesto@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('administrator');
        $role->users()->attach($user);
        $user = Sentinel::findByCredentials([
            'email' => 'jzazueta@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('administrator');
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
            'email' => 'ernesto@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('administrator');
        $role->users()->detach($user);
        $user = Sentinel::findByCredentials([
            'email' => 'jzazueta@tmake.mx',
        ]);
        $role = Sentinel::findRoleBySlug('administrator');
        $role->users()->detach($user);
        Sentinel::findRoleBySlug('administrator')->delete();
        Sentinel::findRoleBySlug('worker')->delete();
        Sentinel::findRoleBySlug('customer')->delete();
    }
}