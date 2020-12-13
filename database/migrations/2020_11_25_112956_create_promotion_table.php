<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date_start');
            $table->date('date_end');
            $table->integer('min_shopping')->default(0);
            $table->string('type_promo',50)->comment('Diskon,Tebus Murah,Gratis Produk');
            $table->integer('option_promo')->default(0)->comment('hanya untuk tebus murah, 1 wajib ambil smua, 2 hanya pilih salah satu');
            $table->integer('total')->default(0);
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('promotion');
    }
}
