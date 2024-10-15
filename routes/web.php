<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

Route::any('ajax/{action}', 'App\Http\Controllers\AjaxController@_html')->name('ajax');

Route::get('auth', 'App\Http\Controllers\ClientController@auth')->name('auth');

Route::get('vkapp', 'App\Http\Controllers\VKappController@index')->name('vkapp');

Route::get('/', 'App\Http\Controllers\IndexController@index')->name('main');

//Route::get('/astro', '\App\Http\Controllers\TelegramController@handleCallback')->name('astro');
Route::get('/astro', '\App\Http\Controllers\TelegramController@index')->name('astro');
Route::post('/tga', '\App\Http\Controllers\TelegramController@auth')->name('tgauth');
Route::post('/addfields', '\App\Http\Controllers\ClientController@addfields')->name('addfields');
Route::post('/astroview', '\App\Http\Controllers\TelegramController@astroview')->name('astroview');

Auth::routes();

// Маршрут для выхода
Route::post('/logout', function () {
    Cookie::expire('client_id');
// Перенаправление на главную страницу после выхода
    return redirect('/');
})->name('logout');
