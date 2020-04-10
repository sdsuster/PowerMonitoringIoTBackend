<?php

use Illuminate\Http\Request;
/*
|------ --------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('auth/login', 'ApiController@login');

Route::post('auth/register', 'ApiController@register');

// Route::group(['middleware' => 'auth.jwt'], function () {


//     // Route::get('products', 'ProductController@index');
//     // Route::get('products/{id}', 'ProductController@show');
//     // Route::post('products', 'ProductController@store');
//     // Route::put('products/{id}', 'ProductController@update');
//     // Route::delete('products/{id}', 'ProductController@destroy');
// });

Route::get('auth/refresh', 'ApiController@refresh');
Route::middleware('auth:api')->group(function () {
    Route::get('logout', 'Api   Controller@logout');
    Route::get('auth/user', 'ApiController@getAuthUser');

    Route::resource('kampus', 'KampusController');

    Route::resource('gedung', 'GedungController');
    Route::get('gedung_h', 'GedungController@getHeader');

    Route::resource('lantai', 'LantaiController');
    Route::get('lantai_h', 'LantaiController@getHeader');

    Route::resource('perangkat', 'PerangkatController');
    Route::delete('perangkat_d/{parentId}', 'PerangkatController@destroyByParrent');
    Route::get('perangkat_h', 'PerangkatController@getHeader');
    Route::get('perangkat_t', 'PerangkatController@getTree');
    Route::post('perangkat/generatePass', 'PerangkatController@generateHashPass');
});
// Route::post('perangkat/generatePass', 'PerangkatController@generateHashPass');
Route::resource('detailPerangkat', 'DetailPerangkatController')->middleware('detailPerangkat');
Route::get('ringkasanPerangkat/{id}', 'ringkasanPerangkatController@getDayPower');
Route::get('ringkasanPerangkat1/{id}', 'ringkasanPerangkatController@getDaySummary');

// Route::get('test', function () {
//     event(new App\Events\InjectPerangkat('22132'));
//     return "Event has been sent!";
// });
