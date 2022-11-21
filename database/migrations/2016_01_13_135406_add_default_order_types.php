<?php

use Illuminate\Database\Migrations\Migration;

class AddDefaultOrderTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $defaultTypes = [
            '1004: Uniform Residential Appraisal Report',
            '1004C: Manufactured Home Appraisal',
            '1025: Small Residential Income Property Appraisal Report',
            '1073: Individual Condo Unit Appraisal Report',
            'FHA 1004: FHA Uniform Residential Appraisal Report',
            'FHA 1025: FHA Small Residential Income Property Appraisal Report',
            '2055: Exterior Only Inspection Residential Appraisal Report',
            '1075: Exterior Only Condo',
        ];
        foreach ($defaultTypes as $type) {
            \App\Models\OrderType::create([
                'name' => $type
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
//        DB::table('order_types')->truncate();
    }
}
