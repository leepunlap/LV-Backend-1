<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAircraftDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aircraft_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aircrafts_id');
            $table->foreignId('company_id');
            $table->foreignId('operator_id');
            $table->string('fuel_burn_per_hour', 20)->nullable();
            $table->string('capacity', 100)->nullable();
            $table->string('crew_per_diem', 100)->nullable();
            $table->string('crew_hotel', 100)->nullable();
            $table->string('margin', 100)->nullable();
            $table->string('passenger', 100)->nullable();
            $table->string('beds_person', 100)->nullable();
            $table->string('wifi', 100)->nullable();
            $table->string('satellite_phone', 100)->nullable();
            $table->string('baggage_capacity', 100)->nullable();
            $table->string('no_of_crew', 10)->nullable();
            $table->string('hourly_rate', 10)->nullable();
            $table->string('total_crew_cost', 10)->nullable();
            $table->enum('status', ['Active', 'Archived', 'Deleted']);
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
        Schema::dropIfExists('aircraft_details');
    }
}
