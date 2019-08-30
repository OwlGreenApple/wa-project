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
Route::post('updateuser', 'HomeController@updateUser')->name('updateuser');//home.blade

/* Lists */
Route::post('addlist','ListController@addList')->name('addlist'); //<--home.blade
Route::get('userlist','ListController@userList')->name('userlist');

/* User Customer */
Route::get('usercustomer/{id_list}','ListController@userCustomer');

/* BroadCast */
Route::get('broadcast','BroadCastController@index')->name('broadcast'); //broadcast.broadcast
Route::get('broadcastform','BroadCastController@FormBroadCast')->name('broadcastform'); //broadcast.broadcastlist
Route::post('createbroadcast','BroadCastController@createBroadCast')->name('createbroadcast'); //broadcast.broadcast
Route::get('broadcastlistcustomer/{id_broadcast}','BroadCastController@displayBroadCastList')->name('broadcastlistcustomer'); //broadcast.broadcastlistcustomer
Route::post('sendbroadcast','BroadCastController@sendBroadCast')->name('sendbroadcast'); 

/* Reminder */
Route::get('reminder','ReminderController@index')->name('reminder'); //reminder.reminder
Route::get('remindercreate','ReminderController@createReminder')->name('remindercreate'); //reminder.reminder
Route::post('reminderadd','ReminderController@addReminder')->name('reminderadd');



