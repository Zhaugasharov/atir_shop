<?php
use App\Http\Controllers\OrderController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/test');
});

Route::get('/order/{orderId}', [OrderController::class, 'order'] );


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/orders', 'HomeController@order')->name('orders');

Route::post('/save-product', 'HomeController@saveProduct')->name('saveProduct');
Route::post('/save-products', 'HomeController@saveProducts')->name('saveProducts');

Route::prefix('api')->group(function(){
    Route::get('/get-key-words', 'ApiController@getKeyWords')->name('apiGetKeyWords');
});
