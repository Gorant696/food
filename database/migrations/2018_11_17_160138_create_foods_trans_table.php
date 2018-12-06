<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodsTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foods_trans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('food_id')->unsigned();
            $table->bigInteger('language_id')->unsigned();
            $table->bigInteger('category_id')->unsigned()->nullable();
            $table->string('title');
            $table->string('description');
            $table->timestamps();

            $table->unique(['food_id','language_id', 'category_id']);
            $table->foreign('food_id')->references('id')->on('foods')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foods_trans');
    }
}
