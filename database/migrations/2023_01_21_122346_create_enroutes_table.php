<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnroutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enroutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_airport_id');
            $table->foreignId('destination_airport_id');
            $table->date('date');
            $table->decimal('subtotal', 15, 2);
            $table->string('subtotal_currency', 45);
            $table->string('total_distance_flown_km', 15, 2);
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
        Schema::dropIfExists('enroutes');
    }
}
