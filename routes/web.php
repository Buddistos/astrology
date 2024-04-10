<?php


Route::any('ajax/{action}', 'AjaxController@_html')->name('ajax');

Route::get('/vkapp', 'VKappController@index');

Route::get('/', function () {
    return view('main');
});

Auth::routes();
