<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameProcessedToCurrentOrderAssignments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_assignments', function (Blueprint $table) {
            $table->renameColumn('processed', 'active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_assignments', function (Blueprint $table) {
            $table->renameColumn('active', 'processed');
        });
    }
}
