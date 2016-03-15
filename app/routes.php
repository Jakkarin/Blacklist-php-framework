<?php use systems\Route;

Route::pattern('numberic', '[0-9]+');
Route::pattern('letter', '[A-z]+');
Route::pattern('string', '[A-z0-9]+');

Route::controller('controller', 'mainController');

Route::get('', 'mainController@index');
Route::get('adminmsd/{numberic}', 'mainController@numberic');
Route::get('adminmsd/{letter}', 'mainController@letter');

Route::middleware('admin', function() {
    Route::get('admin', 'mainController@index');
});

Route::group('group', function() {
    Route::get('group', 'mainController@index');
});

Route::domain('ost.blacklistworld.com', function() {
    Route::get('', 'mainController@index');
});
