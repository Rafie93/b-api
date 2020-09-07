<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaketPromoProductDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paket_promo_product_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('paket_product_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer('quantity');
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
        Schema::dropIfExists('paket_promo_product_detail');
    }
}
