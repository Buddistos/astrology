<?php

Route::any('ajax/{action}', 'App\Http\Controllers\AjaxController@_html')->name('ajax');

Route::get('vkapp', 'App\Http\Controllers\VKappController@index')->name('vkapp');

Route::get('/', 'App\Http\Controllers\IndexController@index')->name('main');

Auth::routes();
