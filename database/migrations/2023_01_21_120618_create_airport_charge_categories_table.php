<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportChargeCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airport_charge_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airport_billing_sources_id');
            $table->foreignId('charge_category_id');
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
        Schema::dropIfExists('airport_charge_categories');
    }
}
