<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->timestamp('date');
            $table->string('notes')->nullable();
            $table->integer('status')->default(0);
            $table->integer('creator_id')->nullable()->unsigned()->index();
            $table->integer('receiver_id')->nullable()->unsigned()->index();
            $table->timestamp('approved_date')->nullable();
            $table->integer('approved_id')->nullable();
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
        Schema::dropIfExists('order_product');
    }
}
