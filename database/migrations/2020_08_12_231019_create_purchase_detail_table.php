<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('purchase_detail_id')->unsigned()->index();
            $table->integer('product_id')->unsigned()->index();
            $table->integer('price')->default(0);
            $table->integer('price_received')->default(0);
            $table->double('quantity')->default(1);
            $table->double('quantity_received')->default(1);
            $table->string('unit')->nullable();
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
        Schema::dropIfExists('purchase_detail');
    }
}
