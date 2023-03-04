<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrouteChargeElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enroute_charge_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enroute_countries_id');
            $table->string('description');
            $table->string('calculation_currency', 45);
            $table->decimal('value', 15, 2);
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
        Schema::dropIfExists('enroute_charge_elements');
    }
}
