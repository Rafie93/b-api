<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->timestamp('date');
            $table->string('notes')->nullable();
            $table->integer('supplier_id')->nullable();
            $table->integer('status')->default(0);
            $table->integer('creator_id')->nullable()->unsigned()->index();
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
        Schema::dropIfExists('purchase');
    }
}
