<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */


use Thomaswelton\LaravelGravatar\Facades\Gravatar;


$factory->define(App\Models\Good::class, function (Faker\Generator $faker) {
    return [
        'state' => $faker->numberBetween(1, 4),
        'user_id' => $faker->numberBetween(1, 5),
        'published_at' => $faker->dateTime,
        'title' => $faker->jobTitle,
        'content' => $faker->paragraph,
        'cover' => $faker->imageUrl(),
        'price' => $faker->randomFloat(1, 1, 100),
        'total' => $faker->numberBetween(1, 100),
        'province' => $faker->city,
        'city' => $faker->city,
        'unit' => '元',
    ];
});

$factory->define(App\Models\Category::class, function (Faker\Generator $faker) {
    $var = $faker->unique()->colorName;
    return [
        'name' => $var,
        'slug' => $var,
    ];
});


$factory->define(App\Models\Tag::class, function (Faker\Generator $faker) {
    $var = $faker->unique()->colorName;
    return [
        'name' => $var,
        'slug' => $var,
    ];
});

$factory->define(App\Models\Address::class, function (Faker\Generator $faker) {
    return [
        'user_id' =>  $faker->unique()->numberBetween(1, 5),
        'name' => $faker->name,
        'phone' => $faker->unique()->phoneNumber,
        'province' => $faker->city,
        'city' => $faker->city,
        'street' => $faker->streetAddress,
        'area' => $faker->streetAddress,
        'post_code' => $faker->postcode,
    ];
});


$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->username,
        'password' => $password ?: $password = bcrypt('secret'),
        'email' => $faker->unique()->safeEmail,
        'avatar' => Gravatar::src($faker->unique()->safeEmail),
        'phone' => $faker->unique()->phoneNumber,
        'sign' => $faker->paragraph,
        'credit' => $faker->numberBetween(1, 100),
        'curr_login_at' => $faker->dateTime,
        'last_login_at' => $faker->dateTime,
        'registered_at' => $faker->dateTime,
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Models\Admin::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'username' => $faker->username,
        'password' => $password ?: $password = bcrypt('secret'),
        'email' => $faker->unique()->safeEmail,
        'avatar' => $faker->imageUrl(60, 60),
        'phone' => $faker->unique()->phoneNumber,
        'role' => $faker->numberBetween(1, 5),
        'curr_login_at' => $faker->dateTime,
        'last_login_at' => $faker->dateTime,
        'registered_at' => $faker->dateTime,
        'remember_token' => str_random(10),
    ];
});
