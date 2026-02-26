<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
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
Route::get('/get-key-words', 'ApiController@getKeyWords')->name('apiGetKeyWords');
Route::get('/products', [ApiController::class, 'products'])->name('apiProducts');
Route::get('/search-suggest', [ApiController::class, 'searchSuggest'])->name('apiSearchSuggest');
Route::get('/brands', [ApiController::class, 'brands'])->name('apiBrands');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::match(['get', 'post'], '/whatsapp/webhook', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handle']);
