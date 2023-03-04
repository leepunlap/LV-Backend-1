<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_airport_id');
            $table->foreignId('destination_airport_id');
            $table->foreignId('aircraft_id');
            $table->string('name');
            $table->text('data');
            $table->tinyInteger('status');
            $table->foreignId('created_by');
            $table->foreignId('updated_by');
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
        Schema::dropIfExists('other_charges');
    }
}
