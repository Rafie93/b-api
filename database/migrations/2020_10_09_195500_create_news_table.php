<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tittle');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->string('category_news')->nullable();
            $table->string('thumbnails')->nullable();
            $table->integer('creator_id');
            $table->tinyInteger('is_status')->default(0)->comment('0=draft,1 is publish, 2 is deleted');
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
        Schema::dropIfExists('news');
    }
}
