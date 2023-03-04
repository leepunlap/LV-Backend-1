<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirportBillingSourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airport_billing_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('airport_charges_id');
            $table->foreignId('billing_source_id');
            $table->string('name');
            $table->decimal('value', 15, 2);
            $table->string('currency', 45);
            $table->enum('type', ['origin', 'destination']);
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
        Schema::dropIfExists('airport_billing_sources');
    }
}
