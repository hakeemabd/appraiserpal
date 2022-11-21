<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('software_id')->unsigned()->nullable();
            $table->foreign('software_id')->references('id')->on('softwares');
            $table->boolean('property_photo')->nullable();
            $table->string('forms_type')->nullable();
            $table->date('effective_date')->nullable();
            $table->integer('category_id')->nullable();
            $table->enum('assignment_type', [
                'purchase',
                'refinance',
                'other'
            ])->nullable();
            $table->enum('occupancy_type', [
                'owner',
                'tenant',
                'vacant'
            ])->nullable();
            $table->enum('financing', [
                'conventional',
                'fha',
                'other'
            ])->nullable();
            $table->enum('property_rights', [
                'free simple',
                'leasehold',
                'other'
            ])->nullable();
            $table->string('specific_instructions')->nullable();
            $table->string('order_instructions')->nullable();
            $table->string('adjustment_type')->nullable();
            $table->float('total')->nullable();
            $table->boolean('full')->nullable();
            $table->boolean('inspection_sheets')->nullable();
            $table->integer('order_type_id')->unsigned()->nullable();
            $table->foreign('order_type_id')->references('id')->on('order_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('orders_user_id_foreign');
            $table->dropForeign('orders_software_id_foreign');
            $table->dropForeign('orders_order_type_id_foreign');
        });
        Schema::drop('orders');
    }
}
