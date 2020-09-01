<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('sale_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer('variant_id')->unsigned()->index()->nullable();
            $table->integer('price_product')->default(0);
            $table->integer('price_sale')->default(0);
            $table->double('quantity')->default(1);
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
        Schema::dropIfExists('sale_detail');
    }
}
