<?php

use Illuminate\Database\Migrations\Migration;

class AddAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Sentinel::registerAndActivate([
            'email' => 'ernesto@tmake.mx',
            'password' => 'developer',
            'first_name' => 'Ernesto',
            'last_name' => 'Ruy Sanchez',
            'mobile_phone' => '6193069094'
        ]);

        Sentinel::registerAndActivate([
            'email' => 'jzazueta@tmake.mx',
            'password' => 'developer',
            'first_name' => 'Jonatthan',
            'last_name' => 'Zazueta',
            'mobile_phone' => '1234567890'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Sentinel::findByCredentials(['email' => 'ernesto@tmake.mx'])->delete();
        Sentinel::findByCredentials(['email' => 'jzazueta@tmake.mx'])->delete();
    }
}
