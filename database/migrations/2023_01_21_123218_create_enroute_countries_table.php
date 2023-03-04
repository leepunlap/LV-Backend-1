<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrouteCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enroute_countries', function (Blueprint $table) {
            $table->id();
            $table->string('enroutes_id');
            $table->string('country');
            $table->decimal('total', 15, 2);
            $table->string('total_currency', 45);
            $table->decimal('distance_flown_in_km', 15, 2);
            $table->decimal('chargeable_distance_in_km', 15, 2);
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
        Schema::dropIfExists('enroute_countries');
    }
}
