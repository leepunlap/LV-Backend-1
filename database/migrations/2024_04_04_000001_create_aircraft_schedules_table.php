<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAircraftSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('aircraft_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aircraft_id');
            $table->integer('airport_id');
            $table->date('schedule_date');
            $table->timestamps();
            $table->unique(['aircraft_id', 'schedule_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('aircraft_schedules');
    }
}
