<?php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LocaleController;
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

Route::get('/', [OrderController::class, 'index'])->name('index');

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('lang.switch');
Route::get('/order/{orderId}', [OrderController::class, 'order'] );
Route::post('/save-order/{orderId}', [OrderController::class, 'saveOrder'] )->name('saveOrder');
Route::get('/order-finish/{orderId}', [OrderController::class, 'orderFinish'])->name('orderFinish');

//Auth::routes();

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/orders', 'HomeController@order')->name('orders');

Route::post('/save-product', 'HomeController@saveProduct')->name('saveProduct');
Route::post('/save-products', 'HomeController@saveProducts')->name('saveProducts');
Route::delete('/delete-product/{id}', 'HomeController@deleteProduct')->name('deleteProduct');
