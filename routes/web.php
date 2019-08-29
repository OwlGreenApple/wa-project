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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web']],function(){
/* Customers */
Route::get('customer/{list_name}','CustomerController@index')->name('customer'); //register-customer.blade
Route::post('customer/add','CustomerController@addCustomer')->middleware('customer')->name('addcustomer');
});

Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');//home.blade

/* Lists */
Route::post('addlist','ListController@addList')->name('addlist'); //<--home.blade
Route::get('userlist','ListController@userList')->name('userlist');

/* User Customer */
Route::get('usercustomer/{id_list}','ListController@userCustomer');

/* BroadCast */
Route::get('broadcast','BroadCastController@index')->name('broadcast'); //broadcast.broadcast

/* Reminder */
Route::get('reminder','ReminderController@index')->name('reminder'); //reminder.reminder



