<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSaleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sale', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->integer('customer_id')->unsigned()->index();
            $table->date('date');
            $table->time('time');
            $table->integer('total_befor_tax');
            $table->integer('total_price');
            $table->integer('total_price_product');
            $table->integer('total_shipping');
            $table->integer('total_service');
            $table->integer('total_tax');
            $table->integer('discount');
            $table->tinyInteger('status')->default(0);
            $table->string('payment_methode')->default(1);
            $table->string('notes')->nullable();
            $table->string('coupon')->nullable();
            $table->integer('creator_id')->nullable()->unsigned()->index();
            $table->integer('invoice_id')->nullable()->unsigned()->index();
            $table->softDeletes();
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
        Schema::dropIfExists('sale');
    }
}
