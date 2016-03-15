<?php use systems\Route;

Route::pattern('numberic', '[0-9]+');
Route::pattern('letter', '[A-z]+');
Route::pattern('string', '[A-z0-9]+');

Route::controller('controller', 'MainController');

Route::get('', 'MainController@index');
Route::post('', 'MainController@index');
Route::get('adminmsd/{numberic}', 'MainController@numberic');
Route::get('adminmsd/{letter}', 'MainController@letter');

Route::middleware('admin', function() {
    Route::get('admin', 'MainController@index');
});

Route::group('group', function() {
    Route::get('group', 'MainController@index');
});

Route::domain('ost.blacklistworld.com', function() {
    Route::get('', 'MainController@index');
});
