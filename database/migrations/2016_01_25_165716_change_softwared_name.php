<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Softwares;

class ChangeSoftwaredName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $softwares = [
            1 => 'WinTOTAL 2013',
            2 => 'WinTOTAL AURORA',
            3 => 'ACI',
            4 => 'appraise it',
            5 => 'Click Forms',
        ];
        foreach($softwares as $id => $value) {
            Softwares::where('id', '=', $id)->update([
                'name' => $value,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('softwares', function (Blueprint $table) {
            //
        });
    }
}
