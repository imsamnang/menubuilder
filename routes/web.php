<?php

Route::get('/', function () {
    return view('layouts.layout');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

// Routes for the menu admin
Route::group(array('prefix' => 'admin/menu'), function()
{
    // Showing the admin for the menu builder and updating the order of menu items
    Route::get('/', 'Admin\MenuController@getIndex');
    Route::post('/', 'Admin\MenuController@postIndex');

    Route::post('new', 'Admin\MenuController@postNew');
    Route::post('delete', 'Admin\MenuController@postDelete');

    Route::get('edit/{id}', 'Admin\MenuController@getEdit');
    Route::post('edit/{id}', 'Admin\MenuController@postEdit');
});

  // For Card
  Route::get('/zmenu','Admin\ZMenuController@index');  
  Route::post('/zmenu/save_menu','Admin\ZMenuController@save_menu');  
  Route::post('/zmenu/save','Admin\ZMenuController@save');  
  Route::post('/zmenu/delete','Admin\ZMenuController@delete');