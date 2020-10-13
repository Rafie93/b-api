<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRatingUlasanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_rating_ulasan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->unsigned()->index();
            $table->longText('ulasan')->nullable();
            $table->text('image')->nullable();
            $table->integer('rating')->default(1);
            $table->integer('creator_id')->unsigned()->index();
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
        Schema::dropIfExists('product_rating_ulasan');
    }
}
