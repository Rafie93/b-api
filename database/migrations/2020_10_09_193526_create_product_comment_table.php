<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCommentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_comment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->unsigned()->index();
            $table->longText('comment')->nullable();
            $table->integer('creator_id')->unsigned()->index();
            $table->string('to')->default('admin');
            $table->string('type')->default('user');
            $table->integer('is_read')->default(0);
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
        Schema::dropIfExists('product_comment');
    }
}
