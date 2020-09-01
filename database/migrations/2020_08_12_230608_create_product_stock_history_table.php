<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stock_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('date');
            $table->integer('product_id')->unsigned()->index();
            $table->double('quantity')->default(0);
            $table->string('unit')->nullable();
            $table->integer('source')->default(0);
            $table->string('ref_code')->nullable();
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
        Schema::dropIfExists('product_stock_history');
    }
}
