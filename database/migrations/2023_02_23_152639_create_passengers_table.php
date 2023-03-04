<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePassengersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->default(0);
            $table->string('first_name');
            $table->string('last_name');
            $table->string('title');
            $table->string('email');
            $table->string('phone');
            $table->string('passport_number');
            $table->string('country_of_issue');
            $table->date('expiry_date');
            $table->text('concierge');
            $table->tinyText('request');
            $table->tinyInteger('have_pet');
            $table->string('type_of_pet')->nullable();
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
        Schema::dropIfExists('passengers');
    }
}
