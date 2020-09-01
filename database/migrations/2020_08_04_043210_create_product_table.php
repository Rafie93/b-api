<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku',50)->nullable();
            $table->string('barcode_type',20)->nullable();
            $table->string('barcode',50)->nullable();
            $table->integer('category_id')->unsigned()->index();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->string('brand')->nullable();
            $table->integer('price')->nullable(); // harga jual
            $table->text('thumbnail')->nullable();
            $table->longText('image')->nullable(); // image url array bisa lebih dari 1 gambar;
            $table->integer('alert_quantity')->nullable();
            $table->tinyInteger('tax_id')->nullable();
            $table->tinyInteger('is_tax_method')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->softDeletes('deleted_at', 0);
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
        Schema::dropIfExists('product');
    }
}
