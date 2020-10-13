<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_account', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bank_name', 50)->unique();
            $table->string('bank_logo')->nullable();
            $table->string('bank_account_no', 50)->unique();
            $table->string('bank_account_name', 50);
            $table->string('description')->nullable();
            $table->boolean('is_active')->unsigned()->default(1);
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
        Schema::dropIfExists('bank_account');
    }
}
