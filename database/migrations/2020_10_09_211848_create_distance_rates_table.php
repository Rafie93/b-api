<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistanceRatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distance_rates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('km');
            $table->integer('min_km');
            $table->integer('max_km');
            $table->integer('prices');
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
        Schema::dropIfExists('distance_rates');
    }
}
