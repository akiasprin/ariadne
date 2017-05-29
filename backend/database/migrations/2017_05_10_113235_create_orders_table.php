<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('sum', 12, 2);
            $table->integer('state')->default(1);
            $table->string('express_name')->nullable();
            $table->string('express_code')->nullable();
            $table->integer('merchant_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('users');
            $table->integer('customer_id')->unsigned();
            $table->foreign('customer_id')->references('id')->on('users');
            $table->integer('address_id')->unsigned();
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->timestamps();
        });

        Schema::create('order_timelines', function (Blueprint $table) {
            $table->integer('order_id')->unsigned()->index();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->integer('state');
            $table->integer('operated_user_id')->unsigned()->references('id')->on('user');
            $table->timestamp('operated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
