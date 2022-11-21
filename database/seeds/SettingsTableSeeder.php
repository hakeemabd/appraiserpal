<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('settings')->delete();

        DB::table('settings')->insert(
            [
                [
                	'id' => 1,
                    'key' => 'global_order_timer',
                    'value' => '1440',
                    'user_id' => 1,
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                ],
                [
                	'id' => 2,
                    'key' => 'idle_order_time',
                    'value' => '1440',
                    'user_id' => 1,
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                ]
            ]
        );
    }
}
