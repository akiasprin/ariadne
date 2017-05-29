<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->unsigned();
            $table->string('name');
            $table->string('slug');
        });

        Schema::create('category_relationships', function (Blueprint $table) {
            $table->integer('good_id')->unsigned()->index();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->integer('category_id')->unsigned()->index();
            $table->foreign('category_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
