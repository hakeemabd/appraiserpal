<?php

use App\Models\Softwares;
use Illuminate\Database\Migrations\Migration;

class UpdateDefaultSoftware extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $path = '/images/softwares/';
        $pics = [
            '1' => 'soft_logos_wintotal.png',
            '2' => 'soft_logos_aurora.png',
            '3' => 'soft_logos_aci.png',
            '4' => 'soft_logos_appraiseit.png',
            '5' => 'soft_logos_clickforms.png'
        ];
        foreach ($pics as $id => $pic) {
            $software = Softwares::find($id);
            $software->location = $path . $pic;
            $software->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
