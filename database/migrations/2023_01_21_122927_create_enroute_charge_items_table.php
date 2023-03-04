<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrouteChargeItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enroute_charge_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enroute_charge_elements_id');
            $table->string('description');
            $table->decimal('value', 15, 2);
            $table->decimal('original_value', 15, 2);
            $table->string('calculation_currency', 45);
            $table->string('original_currency', 45);
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
        Schema::dropIfExists('enroute_charge_items');
    }
}
