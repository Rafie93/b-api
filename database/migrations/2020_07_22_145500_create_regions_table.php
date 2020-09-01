<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provinces', function (Blueprint $table) {
            $table->char('id', 2)->primary('id');
            $table->string('name');
        });

        Schema::create('citys', function (Blueprint $table) {
            $table->char('id', 4)->primary();
            $table->char('province_id', 2)->index();
            $table->string('name');
        });

        Schema::create('districts', function (Blueprint $table) {
            $table->char('id', 7)->primary();
            $table->char('city_id', 4)->index();
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('provinces');
        Schema::dropIfExists('citys');
        Schema::dropIfExists('districts');
    }
}
