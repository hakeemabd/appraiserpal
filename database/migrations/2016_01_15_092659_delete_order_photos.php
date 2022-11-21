<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteOrderPhotos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('order_photos');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('order_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('label_id');
            $table->integer('order_id');
            $table->integer('attachment_id');
            $table->timestamps();
        });
    }
}
