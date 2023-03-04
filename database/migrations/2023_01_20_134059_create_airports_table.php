<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('airport_id');
            $table->string('name');
            $table->string('city');
            $table->foreignId('city_id');
            $table->string('country');
            $table->foreignId('country_id');
            $table->string('icao', 5);
            $table->string('iata', 5);
            $table->decimal('latitude', 12, 8);
            $table->decimal('longitude', 12, 8);
            $table->string('altitude', 10);
            $table->string('timezone', 5);
            $table->string('dst', 5);
            $table->string('tz');
            $table->string('type', 10);
            $table->string('fuel_price', 10);
            $table->enum('status', ['Active', 'Inactive', 'Archived', 'Deleted']);
            $table->tinyInteger('isdeleted');
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
        Schema::dropIfExists('airports');
    }
}
