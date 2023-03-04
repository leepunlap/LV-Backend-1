<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('state_id');
            $table->string('state_code');
            $table->foreignId('country_id');
            $table->char('country_code', 8);
            $table->decimal('latitude', 12, 8);
            $table->decimal('longitude', 12, 8);
            $table->tinyInteger('flag');
            $table->string('wikiDataId');
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
        Schema::dropIfExists('cities');
    }
}
