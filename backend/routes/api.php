<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['middleware' => 'auth:api'], function()
{
    Route::resource('/address', 'Address\AddressController');
    Route::resource('/order', 'Order\OrderController');
    Route::resource('/upload', 'Upload\UploadController');
});

Route::resource('/category', 'Category\CategoryController');
Route::resource('/comment', 'Comment\CommentController');
Route::resource('/good', 'Good\GoodController');

Route::resource('/cart', 'Cart\CartController');

Route::group(['prefix' => 'user','namespace' => 'User'], function ($router)
{
    $router->post('sendcode', 'UserController@sendRegCode');
    $router->post('register', 'UserController@register');
    $router->post('login', 'UserController@login');
    $router->post('logout', 'UserController@logout')->middleware('auth:api');
    $router->get('', 'UserController@index');
    $router->get('{id}', 'UserController@show');
    $router->post('{id}', 'UserController@modify')->middleware('auth:api');
});

Route::get('/', function () {
    dump(Illuminate\Support\Facades\Auth::user()->id);
})->middleware('auth:api');