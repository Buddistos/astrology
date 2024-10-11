<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

Route::any('ajax/{action}', 'App\Http\Controllers\AjaxController@_html')->name('ajax');

Route::get('auth', 'App\Http\Controllers\ClientController@auth')->name('auth');

Route::get('vkapp', 'App\Http\Controllers\VKappController@index')->name('vkapp');

Route::get('/', 'App\Http\Controllers\IndexController@index')->name('main');

//Route::get('/astro', '\App\Http\Controllers\TelegramController@handleCallback')->name('astro');
Route::get('/astro', '\App\Http\Controllers\TelegramController@showView')->name('astro');

Auth::routes();

// Маршрут для выхода
Route::post('/logout', function () {
    Cookie::expire('client_id');
// Перенаправление на главную страницу после выхода
    return redirect('/');
})->name('logout');
