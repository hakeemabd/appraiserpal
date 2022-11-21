<?php

use App\Models\Softwares;
use Illuminate\Database\Migrations\Migration;

class AddSoftwares extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = 'app/softwares/';
        Softwares::create([
            'name' => 'wintotal',
            'location' => $path . 'soft_logos_wintotal.png',
        ]);
        Softwares::create([
            'name' => 'aurora',
            'location' => $path . 'soft_logos_aurora.png',
        ]);
        Softwares::create([
            'name' => 'aci',
            'location' => $path . 'soft_logos_aci.png',
        ]);
        Softwares::create([
            'name' => 'appraiseit',
            'location' => $path . 'soft_logos_appraiseit.png',
        ]);

        Softwares::create([
            'name' => 'clickforms',
            'location' => $path . 'soft_logos_clickforms.png',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('softwares')->truncate();
    }
}
