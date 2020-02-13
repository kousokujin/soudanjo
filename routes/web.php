<?php

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

Route::get('/', 'nologin_view@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/new_quest_wanted','HomeController@new_quest')->name('new_quest');
Route::post('/newquest_register','HomeController@newquest_register')->name('newquest_register');
Route::get('/quests/{id}','nologin_view@show_quest');
Route::post('/quests/join','HomeController@event_join');
Route::get('/cancel/{id}','HomeController@event_cancel');
Route::get('/edit/{id}','HomeController@edit_event');
Route::post('/event_modify','HomeController@event_modify');
Route::get('/delete/{id}','HomeController@event_delete');
Route::get('/edit_profile/{userid}','HomeController@edit_profile');
Route::post('/modify_profile','HomeController@modify_profile');
Route::get('password/{userid}','HomeController@edit_password');
Route::post('/modify_password','HomeController@modify_password');
Route::get('/icon','HomeController@icon');

Route::get('/ogp.png', 'nologin_view@ogp');
Route::get('/discord_icon.png','nologin_view@discord_icon');