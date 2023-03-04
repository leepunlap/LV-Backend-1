<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airport_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_airport_id');
            $table->foreignId('destination_airport_id');
            $table->date('date');
            $table->string('equipment');
            $table->string('equipment_code');
            $table->foreignId('equipment_id');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('origin_charges_total', 15, 2);
            $table->string('origin_charges_total_currency', 45);
            $table->decimal('destination_charges_total', 15, 2);
            $table->string('destination_charges_total_currency', 45);
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
        Schema::dropIfExists('airport_charges');
    }
}
