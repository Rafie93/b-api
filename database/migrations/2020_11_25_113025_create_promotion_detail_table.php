<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('promotion_id')->unsigned()->index();
            $table->integer('product_id')->nullable()->unsigned()->index();
            $table->integer('price_promo');
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
        Schema::dropIfExists('promotion_detail');
    }
}
