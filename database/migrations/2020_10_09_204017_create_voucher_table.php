<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_voucher');
            $table->string('description')->nullable();
            $table->string('jenis_voucher')->default('potongan')->comment('potongan, ongkir, cashback');
            $table->integer('nilai');
            $table->integer('maksimal');
            $table->integer('maksimal_user');
            $table->string('jenis_nilai')->default('rupiah')->comment('rupiah, persen');
            $table->date('berlaku_start');
            $table->date('berlaku_end');
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
        Schema::dropIfExists('voucher');
    }
}
