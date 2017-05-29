<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('title');
            $table->string('desc', 16);
            $table->string('cover');
            $table->longText('content');
            $table->decimal('price', 10, 2);
            $table->integer('total');
            $table->string('unit');
            $table->string('province');
            $table->string('city');
            $table->integer('state');
            $table->integer('views')->default(0);
            $table->integer('sales')->default(0);
            $table->integer('quality');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_idZ')->references('id')->on('users');
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();
        });

        Schema::create('carts', function(Blueprint $table){
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('good_id')->unsigned()->index();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->integer('quantity')->unsigned();
        });

        Schema::create('order_details', function(Blueprint $table){
            $table->integer('order_id')->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('good_id')->unsigned()->index();
            $table->foreign('good_id')->references('id')->on('goods');
            $table->integer('quantity')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('goods');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('good_tag');
        Schema::dropIfExists('good_category');
        Schema::dropIfExists('good_order');
    }
}
