<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unsigned();;
            $table->string('email')->unique();
            $table->string('password');
            $table->string('name')->unique();
            $table->string('phone')->unique();
            $table->string('avatar');
            $table->longText('sign')->nullable();
            $table->integer('credit')->default(0);
            $table->timestamp('curr_login_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('registered_at')->useCurrent();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
