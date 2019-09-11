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

Route::get('ck','ListController@test');
Route::get('justcarbon','EventController@JUSTCARBON');

Auth::routes();
/* User Customer */

Route::get('/home', 'HomeController@index')->name('home');//home.blade
Route::post('updateuser', 'HomeController@updateUser')->name('updateuser');//home.blade

Route::group(['middleware'=>['auth','web']],function(){
	/* Lists */
	Route::get('usercustomer/{id_list}','ListController@userCustomer');
	Route::get('createlist','ListController@listForm')->name('createlist');
	Route::post('addlist','ListController@addList')->middleware('userlist')->name('addlist'); 
	Route::get('userlist','ListController@userList')->name('userlist');
	Route::post('listupload','ListController@uploadListImage')->name('listupload');
	Route::get('browseupload','ListController@browserUploadedImage')->name('browseupload');
	Route::get('displaylistcontent','ListController@displayListContent')->name('displaylistcontent');
	Route::post('updatelistcontent','ListController@updateListContent')->name('updatelistcontent');

	/* BroadCast */
	Route::get('broadcast','BroadCastController@index')->name('broadcast');
	// form to create broadcast
	Route::get('broadcastform','BroadCastController@FormBroadCast')->name('broadcastform');
	//insert broadcast and broadcast-customer data
	Route::post('createbroadcast','BroadCastController@createBroadCast')->name('createbroadcast');
	//see broadcast customer
	Route::get('broadcast_customer','BroadCastController@displayBroadCastCustomer')->name('broadcast_customer');

	/* Reminder */
	Route::get('reminder','ReminderController@index')->name('reminder'); 
	// form to create reminder
	Route::get('reminderform','ReminderController@reminderForm')->name('reminderform'); 
	// set reminder into database
	Route::post('reminderadd','ReminderController@addReminder')->name('reminderadd');
	// retrieve data from reminder customer
	Route::get('reminder_customer','ReminderController@displayReminderCustomers')->name('reminder_customer');
	// change reminder's status
	Route::get('reminder-status/{id_reminder}/{status}','ReminderController@setReminderStatus');
	Route::post('remindermessage','ReminderController@updateReminderMessage')->name('remindermessage');

	/* Event */
	Route::get('event','EventController@index')->name('event');
	Route::get('eventform','EventController@eventForm')->name('eventform');
	Route::post('addevent','EventController@addEvent')->name('addevent');
	Route::get('eventcustomer','EventController@displayEventCustomers')->name('eventcustomer');


	/* Templates */
	Route::get('templates','TemplatesController@templateForm')->name('templates');
	//insert into database broadcast template
	Route::post('addtemplate','TemplatesController@createTemplate')->middleware('template')->name('addtemplate');
	//get broadcast template list name
	Route::get('templatelist','TemplatesController@displayTemplateList')->name('templatelist');
	// get message from broadcast template
	Route::get('displaytemplate','TemplatesController@displayTemplate')->name('displaytemplate');
	// update template
	Route::post('updatetemplate','TemplatesController@updateTemplate')->middleware('template')->name('updatetemplate');
	// delete template
	Route::get('deletetemplate','TemplatesController@delTemplate')->name('deletetemplate');

	/* Senders */
	Route::post('addsender','SenderController@addSender')->name('addsender');
});

/* Customers */
Route::post('customer/add','CustomerController@addCustomer')->middleware('customer')->name('addcustomer');
/* Customer registration */
Route::get('/{list_name}','CustomerController@index'); //register-customer.blade
