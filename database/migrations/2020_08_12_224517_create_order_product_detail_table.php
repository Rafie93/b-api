<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('order_product_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->double('quantity_order')->default(1);
            $table->string('unit')->nullable();
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('order_product_detail');
    }
}
