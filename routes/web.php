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

Route::get('logs-0312', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/', function () {
    return view('welcome');
})->name('visitor');

//Route::get('ck','ListController@generateRandomListName');
Route::get('send','SenderController@getDeviceId');

Route::get('justcarbon','EventController@JUSTCARBON');

/* API */
Route::get('testapi','ApiController@testapi');
Route::get('testcoupon','ApiController@testcoupon');
Route::get('testmail','ApiController@testmail');
Route::get('testpay','ApiController@testpay');
Route::get('testdirectsendwa','ApiController@testDirectSendWA')->name('testdirectsendwa');
Route::get('testdirectsendmail','ApiController@testDirectSendMail')->name('testdirectsendmail');

Route::post('is_pay','ApiController@customerPay');
Route::post('private-list','ApiController@register_list');


Auth::routes();
/* User Customer */

Route::get('/home', 'HomeController@index')->middleware('cors')->name('home');//home.blade
Route::post('updateuser', 'HomeController@updateUser')->name('updateuser');//home.blade

Route::group(['middleware'=>['auth','web','is_admin']],function(){
	Route::get('superadmin', 'AdminController@index');//home.blade
	Route::get('loginuser/{id_user}', 'AdminController@LoginUser');//home.blade
	Route::get('csvimport', 'AdminController@importCSVPage')->name('csvimport');//home.blade
	Route::post('importcustomercsv','AdminController@importCustomerCSV')->name('importcustomercsv');
});

Route::group(['middleware'=>['auth','web']],function(){
	Route::get('tesdevice','DeviceController@test')->name('tesdevice');

	/* Create Device */
	Route::get('registerdevice','CreateDeviceController@index')->name('registerdevice');
	Route::post('createdevice', 'CreateDeviceController@createDevice')->middleware('checkdevicename')->name('createdevice');
	Route::get('deviceauthorize', 'CreateDeviceController@deviceAuthorize');

	Route::get('devicepackage','CreateDeviceController@devicePackage')->name('devicepackage');
	Route::get('devicecheckout','CreateDeviceController@checkout')->name('devicecheckout');
	Route::get('thanks','CreateDeviceController@thankYou')->name('thanks');
	#delete if not needed anymore
	Route::get('temporary','CreateDeviceController@temporary')->name('temporary');
	//Route::get('testcurl','CreateDeviceController@testcurl')->name('testcurl');
	#delete if not needed anymore

	/* Device status and profile */
	Route::get('devices','DeviceController@deviceList')->name('devices');
	Route::get('authorize/{device_id}', 'DeviceController@getScanBarcodeAuthorize');
	Route::get('scan', 'DeviceController@getScanBarcodeChangePhone')->name('scan');
	Route::get('devicedetail/{device_id}', 'DeviceController@getDetailDevice')->name('devicedetail');
	Route::get('devicestatus/{device_id}', 'DeviceController@getStatusDevice');
	Route::post('updatenumber', 'DeviceController@updateNumber')->name('updatenumber');

	/* Lists */
	Route::get('usercustomer/{id_list}','ListController@userCustomer');
	Route::get('createlist','ListController@listForm')->name('createlist');
	Route::post('addlist','ListController@addList')->middleware('userlist')->name('addlist'); 
	Route::get('userlist','ListController@userList')->name('userlist');
	Route::get('browseupload','ListController@browserUploadedImage')->name('browseupload');
	Route::get('displaylistcontent','ListController@displayListContent')->name('displaylistcontent');
	Route::post('updatelistcontent','ListController@updateListContent')->middleware('checkadditional')->name('updatelistcontent');
	Route::get('deletelistcontent','ListController@delListContent')->name('deletelistcontent');
	Route::get('editdropfields','ListController@editDropfields')->name('editdropfields');
	Route::post('insertoptions','ListController@insertOptions')->name('insertoptions');
	Route::post('insertfields','ListController@insertFields')->name('insertfields');
	Route::post('insertdropdown','ListController@insertDropdown')->name('insertdropdown');
	Route::post('duplicatelist','ListController@duplicateList')->name('duplicatelist');
	Route::post('exportlistsubscriber','ListController@exportListSubscriber')->name('exportlistsubscriber');
	Route::get('export_csv_list_subscriber/{id_list}','ListController@exportListCSVSubscriber');
	Route::post('import_csv_list_subscriber','ListController@importCSVListSubscribers');

	/* Additional */
	Route::get('delfield','ListController@delField')->name('delfield');
	Route::post('updateadditional','ListController@updateField')->name('updateadditional');
	Route::get('displayajaxfield','ListController@displayAjaxAdditional')->name('displayajaxfield');
	Route::get('customeradditional','ListController@customerAdditional')->name('customeradditional');

	/* BroadCast */
	Route::get('broadcast','BroadCastController@index')->name('broadcast');
	// form to create broadcast reminder
	Route::get('broadcastform','BroadCastController@FormBroadCast')->name('broadcastform');
	// form to create broadcast event
	Route::get('broadcasteventform','BroadCastController@eventFormBroadCast')->name('broadcasteventform');
	//insert broadcast and broadcast-customer data
	Route::post('createbroadcast','BroadCastController@createBroadCast')->name('createbroadcast');
	//see broadcast customer
	Route::get('broadcast_customer','BroadCastController@displayBroadCastCustomer')->name('broadcast_customer');

	/* Reminder */
	Route::get('reminder','ReminderController@index')->name('reminder'); 
	Route::get('reminderlist','ReminderController@displayReminderList')->name('reminderlist'); 
	// form to create reminder
	Route::get('reminderform','ReminderController@reminderForm')->name('reminderform'); 
	// set reminder into database
	Route::post('reminderadd','ReminderController@addReminder')->name('reminderadd');
	// retrieve data from reminder customer
	Route::get('reminder_customer','ReminderController@displayReminderCustomers')->name('reminder_customer');
	// change reminder's status
	Route::get('reminder-status/{id_reminder}/{status}','ReminderController@setReminderStatus');
	Route::post('remindermessage','ReminderController@updateReminderMessage')->name('remindermessage');
	Route::post('reminderdays','ReminderController@updateReminderDays')->name('reminderdays');
	Route::get('delreminder','ReminderController@delReminder')->name('delreminder');
	Route::get('export_reminder_subscriber','ReminderController@exportSubscriber')->name('export_reminder_subscriber');
	Route::get('export_reminder_csv/{id_list}','ReminderController@exportReminderSubscriber');
	
	// reminder auto reply
	Route::get('reminderautoreply','ReminderController@reminderAutoReply')->name('reminderautoreply');
	Route::post('addreminderautoreply','ReminderController@addReminderAutoReply')->name('addreminderautoreply');

	/* Event */
	Route::get('event','EventController@index')->name('event');
	Route::get('eventlist','EventController@displayEventList')->name('eventlist');
	# auto reply event
	Route::get('eventautoreply','EventController@eventAutoReply')->name('eventautoreply');
	Route::post('addeventautoreply','EventController@addEventAutoReply')->name('addeventautoreply');
	Route::get('eventautoreplyturn/{id}/{status}','EventController@turnEventAutoReply');
	Route::get('eventstatus/{id}/{status}','EventController@setEventStatus');

	# scheduled event
	Route::get('eventform','EventController@eventForm')->name('eventform');
	Route::post('addevent','EventController@addEvent')->name('addevent');
	Route::get('eventcustomer','EventController@displayEventCustomers')->name('eventcustomer');
	Route::get('displayeventschedule','EventController@displayEventSchedule')->name('displayeventschedule');
	Route::post('updatevent','EventController@updateEvent')->name('updatevent');
	Route::get('deletevents','EventController@delEvent')->name('deletevents');
	Route::get('exportsubscriber','EventController@exportSubscriber')->name('exportsubscriber');
	Route::get('export_csv/{id_list}','EventController@exportEventSubscriber');
	Route::post('import_csv_ev','EventController@importCSVEvent')->name('import_csv_ev');

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
	Route::post('addsender','SenderController@addSender')->middleware('wanumber')->name('addsender');

	/* CKEditor */
	Route::get('ckbrowse', 'CKController@ck_browse')->name('ckbrowse');
	Route::get('ckdelete', 'CKController@ck_delete_image')->name('ckdelete');
	Route::post('ckupload', 'CKController@ck_upload_image')->name('ckupload');
});

/* Customers */
Route::post('customer/add','CustomerController@addCustomer')->middleware('customer')->name('addcustomer');
/* Customer registration */
Route::get('/ev/{list_name}','CustomerController@event'); //register-customer.blade
Route::get('/{list_name}','CustomerController@index'); //register-customer.blade
