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
Route::post('/quests/nouser_join','nologin_view@quest_join');
Route::get('/admin_cancel/{quest_id}/{id}','HomeController@admin_cancel');
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

Route::get('/admin_user','AdminController@show_users');
Route::get('/admin_event','AdminController@show_events');
Route::get('/admin_member','AdminController@show_members');
Route::get('/admin_password_edit/{userid}','AdminController@show_password');
Route::post('/admin_modify_password','AdminController@modify_password');

//Route::get('/get_event','GetEmgController@get_emg');