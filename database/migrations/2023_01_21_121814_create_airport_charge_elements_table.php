<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportChargeElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airport_charge_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airport_charge_categories_id');
            $table->string('name');
            $table->decimal('value', 15, 2);
            $table->string('currency', 45);
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
        Schema::dropIfExists('airport_charge_elements');
    }
}
