<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlightDistancesTable extends Migration
{
    public function up()
    {
        Schema::create('flight_distances', function (Blueprint $table) {
            $table->id();
            $table->integer('origin_airport_id');
            $table->integer('destination_airport_id');
            $table->decimal('distance_km', 10, 2);
            $table->decimal('bearing_deg', 6, 2)->nullable()->comment('Initial bearing in degrees (0-360)');
            $table->timestamps();

            $table->unique(['origin_airport_id', 'destination_airport_id']);
            $table->index('origin_airport_id');
            $table->index('destination_airport_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('flight_distances');
    }
}
