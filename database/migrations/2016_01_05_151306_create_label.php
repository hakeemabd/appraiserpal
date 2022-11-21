<?php

use App\Models\Label;
use Illuminate\Database\Migrations\Migration;

class CreateLabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $labels = ['living room', 'kitchen', 'bathrooms', 'master bedrooms', 'bedrooms', 'master bathrooms', 'exterior front', 'exterior back'];

        foreach ($labels as $label) {
            Label::create([
                'name' => $label,
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
        DB::table('labels')->truncate();
    }
}
