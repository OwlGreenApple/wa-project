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

Route::get('pricing','OrderController@pricing');

/* PROTOTYPE */
//Route::get('createlists', 'HomeController@formList');
//Route::get('lists-create', 'HomeController@createList');
//Route::get('lists', 'HomeController@dataList');
Route::get('add-message-event/{campaign_id}', 'CampaignController@addMessageEvent');
Route::get('add-message-auto-responder/{campaign_id}', 'CampaignController@addMessageAutoResponder');
Route::get('report-reminder', 'HomeController@reportReminder');
Route::get('history-order', 'HomeController@historyOrder');

/* User Customer */
Route::post('updateuser', 'HomeController@updateUser')->name('updateuser');//home.blade

/* Admin */
Route::group(['middleware'=>['auth','web','is_admin']],function(){
	/*Route::get('sendingrate', 'AdminController@SendingRate');
  Route::post('savesettings', 'AdminController@SaveSettings');
  Route::get('superadmin', 'AdminController@index');//home.blade
	Route::get('loginuser/{id_user}', 'AdminController@LoginUser');//home.blade
	Route::get('csvimport', 'AdminController@importCSVPage')->name('csvimport');//home.blade
	Route::post('importcustomercsv','AdminController@importCustomerCSV')->name('importcustomercsv');*/
  Route::get('country-code','AdminController@InsertCountry');
  Route::get('country-show','AdminController@showCountry');
  Route::get('country-del','AdminController@delCountry');
  Route::post('save-country','AdminController@saveCountry')->middleware('check_country');

  //List User 
  Route::get('/list-user','Admin\UserController@index');
  Route::get('/list-user/load-user','Admin\UserController@load_user');
  Route::get('/list-user/add-user','Admin\UserController@add_user');
  Route::get('/list-user/edit-user','Admin\UserController@edit_user');
  Route::get('list-user/view-log','Admin\UserController@load_log');
  Route::post('/import-excel-user','Admin\UserController@import_excel_user');
  
  //admin order
  Route::get('/list-order',function(){
    return view('admin.list-order.index');
  });
  Route::get('/list-order/load-order','Admin\OrderController@load_list_order');
  Route::get('/list-order/confirm','Admin\OrderController@confirm_order');
  
  //admin Woowa
  Route::get('/list-woowa',function(){
    return view('admin.list-woowa.index');
  });
  Route::get('/list-woowa/load-woowa','Admin\OrderController@load_woowa');
  
  //list phone
  Route::get('/list-phone',function(){
    return view('admin.list-phone.index');
  });
  Route::get('/list-phone/load','Admin\PhoneController@load_phone');
});

/* SETTING */
Route::group(['middleware'=>['auth','web']],function(){
  Route::get('settings', 'SettingController@index');
  Route::post('save-settings', 'SettingController@settingsUser')->middleware('usersettings');
  Route::get('load-phone-number', 'SettingController@load_phone_number');
  Route::get('connect-phone', 'SettingController@connect_phone')->middleware('checkcall');
  Route::get('verify-phone', 'SettingController@verify_phone')->middleware('checkphone');
  Route::get('delete-phone', 'SettingController@delete_phone');
  Route::get('check-qr', 'SettingController@check_connected_phone');
  Route::get('delete-api/{no}', 'SettingController@delete_api');
  Route::get('status-nomor/{no}', 'SettingController@status_nomor');
  Route::get('get-qr-code/{no}', 'SettingController@get_qr_code');
  Route::get('qr-status/{no}', 'SettingController@qr_status');
  Route::get('take-screenshoot/{no}', 'SettingController@take_screenshot');
  Route::get('get-all-cust', 'SettingController@get_all_cust');
  Route::get('get-key/{no}', 'SettingController@get_key');
  Route::get('send-message/{wa_number}/{message}/{key}', 'SettingController@send_message');
  // Route::post('edit-phone', 'SettingController@editPhone');
});

/* HOME */
Route::get('/home', 'HomeController@index')->middleware('cors')->name('home');
Route::get('checkphone', 'HomeController@checkPhone');

/*** USER ***/
Route::group(['middleware'=>['auth','web','authsettings']],function(){
  Route::get('google-form','HomeController@google_form');
  Route::get('jsonEncode','HomeController@jsonEncode');

	/* LIST */
  Route::get('lists', 'ListController@index');
  Route::get('lists-table', 'ListController@dataList');
  Route::get('list-form', 'ListController@formList');
  Route::get('list-create', 'ListController@createList');
  Route::post('list-save','ListController@saveList')->name('savelist'); 
  Route::get('list-delete','ListController@delListContent')->name('deletelist');
  Route::get('list-search','ListController@searchList')->name('searchlist');
  Route::get('list-edit/{list_id}','ListController@editList');
  Route::get('list-additional','ListController@additionalList')->name('additionalList');
  Route::get('list-customer','ListController@displaySubscriber');
  Route::get('list-table-customer','ListController@listTableCustomer');
  Route::get('list-delete-customer','ListController@deleteSubscriber');
  Route::post('list-update','ListController@updateListContent')->middleware('checkadditional')->name('listupdate');
  Route::post('list-duplicate','ListController@duplicateList')->name('duplicatelist');
  Route::post('import_excel_list_subscriber','ListController@importExcelListSubscribers')->middleware('checkimportcsv');
  Route::post('changelistname','ListController@changeListName');
  Route::get('export_excel_list_subscriber/{id_list}/{import}','ListController@exportListExcelSubscriber');
  Route::post('save-auto-reply','ListController@save_auto_reply');

  /* ADDITIONAL */
  Route::post('insertoptions','ListController@insertOptions')->name('insertoptions');
  Route::get('browseupload','ListController@browserUploadedImage')->name('browseupload');
  Route::get('editdropfields','ListController@editDropfields')->name('editdropfields');
  Route::post('insertfields','ListController@insertFields')->name('insertfields');
  Route::post('insertdropdown','ListController@insertDropdown')->name('insertdropdown');
  Route::get('delfield','ListController@delField')->name('delfield');
  Route::post('updateadditional','ListController@updateField')->name('updateadditional');
  Route::get('displayajaxfield','ListController@displayAjaxAdditional')->name('displayajaxfield');
  Route::get('customeradditional','ListController@customerAdditional')->name('customeradditional');

  /* CAMPAIGN */
  Route::get('campaign', 'CampaignController@index');
  Route::get('create-campaign', 'CampaignController@CreateCampaign');
  Route::post('save-campaign', 'CampaignController@SaveCampaign');
  Route::get('search-campaign', 'CampaignController@searchCampaign');
  Route::get('campaign-del','CampaignController@delCampaign'); 
  Route::post('edit-campaign-name','CampaignController@editCampaign'); 
  
  /* EVENT */
  Route::get('event-list','EventController@displayEventList')->name('eventlist');
  Route::get('event-del','EventController@delEvent');
  Route::post('event-duplicate','EventController@duplicateEvent')->middleware('checkeventduplicate');
  Route::get('load-event','EventController@loadEvent');
  Route::get('delete-event','EventController@deleteEvent');

  /* REMINDER */
  Route::get('reminder-list','ReminderController@displayReminderList')->name('reminderlist');
  Route::get('reminder-del','ReminderController@delReminder');
  Route::post('reminder-duplicate','ReminderController@duplicateReminder')->middleware('checkresponderduplicate');
  Route::get('load-auto-responder','ReminderController@loadAutoResponder');
  Route::get('delete-auto-responder','ReminderController@deleteAutoResponder');

  /* BROADCAST */
  Route::get('broadcast-list','BroadCastController@displayBroadCast')->name('broadcastlist'); 
  Route::get('broadcast-del','BroadCastController@delBroadcast'); 
  Route::get('broadcast-check','BroadCastController@checkBroadcastType'); 
  Route::post('broadcast-duplicate','BroadCastController@duplicateBroadcast')->middleware('checkbroadcastduplicate'); 

  /* APPOINTMENT */
  Route::get('create-apt','AppointmentController@createAppointment');
  Route::post('save-apt','AppointmentController@saveAppointment')->middleware('save_apt');
  Route::get('display-template-apt','AppointmentController@displayTemplateAppointment');
  Route::post('save-template-appoinments','AppointmentController@saveTemplateAppointment')->middleware('checkeditappt');
  Route::get('appointment','AppointmentController@index')->name('appointment');
  Route::get('list-apt/{id}','AppointmentController@listAppointment');
  Route::get('list-table-apt','AppointmentController@listTableAppointments');
  Route::post('list-edit-apt','AppointmentController@listAppointmentEdit')->middleware('checkeditformappt');
  Route::get('list-delete-apt','AppointmentController@listAppointmentDelete');
  Route::get('table-apt','AppointmentController@tableAppointment');
  Route::get('form-apt/{id}','AppointmentController@formAppointment');
  Route::get('edit-apt/{id}','AppointmentController@editAppointment');
  Route::get('edit-appt-template','AppointmentController@editAppointmentTemplate');
  Route::get('delete-appt-template','AppointmentController@deleteAppointmentTemplate');
  Route::get('display-customer-phone','AppointmentController@displayCustomerPhone');
  Route::post('save-appt-time','AppointmentController@saveAppointmentTime')->middleware('checkformappt');
  Route::get('appt-del','AppointmentController@delAppointment');
  Route::get('export_csv_appt/{campaign_id}','AppointmentController@exportAppointment');

  // scheduled event --OLD CODES
  Route::post('addevent','EventController@addEvent')->name('addevent');
  Route::get('eventform','EventController@eventForm')->name('eventform');
  Route::get('eventcustomer','EventController@displayEventCustomers')->name('eventcustomer');
  Route::get('displayeventschedule','EventController@displayEventSchedule')->name('displayeventschedule');
  Route::post('updatevent','EventController@updateEvent')->name('updatevent');
  Route::get('deletevents','EventController@delEvent')->name('deletevents');
  Route::post('import_csv_ev','EventController@importCSVEvent')->name('import_csv_ev');

  Route::get('event','EventController@index')->name('event');
  // auto reply event
  Route::get('eventautoreply','EventController@eventAutoReply')->name('eventautoreply');
  Route::post('addeventautoreply','EventController@addEventAutoReply')->name('addeventautoreply');
  Route::get('eventautoreplyturn/{id}/{status}','EventController@turnEventAutoReply');
  Route::get('eventstatus/{id}/{status}','EventController@setEventStatus');

  /*old code
	Route::get('usercustomer/{id_list}','ListController@userCustomer');
  Route::get('createlist','ListController@listForm')->name('createlist');
	Route::post('addlist','ListController@addList')->name('addlist'); 
	
	Route::get('userlist','ListController@userList')->name('userlist');
	
	Route::post('exportlistsubscriber','ListController@exportListSubscriber')->name('exportlistsubscriber');
	
	*/
	/* old BroadCast 
	Route::get('broadcast','BroadCastController@index')->name('broadcast');
	// form to create broadcast reminder
	Route::get('broadcastform','BroadCastController@FormBroadCast')->name('broadcastform');
	// form to create broadcast event
	Route::get('broadcasteventform','BroadCastController@eventFormBroadCast')->name('broadcasteventform');
	//insert broadcast and broadcast-customer data
	Route::post('createbroadcast','BroadCastController@createBroadCast')->name('createbroadcast');
	//see broadcast customer
*/
	//old reminder
	// Route::get('reminder','ReminderController@index')->name('reminder'); 
	
	// form to create reminder
	/*Route::get('reminderform','ReminderController@reminderForm')->name('reminderform'); 
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
	*/
	// reminder auto reply
	Route::get('reminderautoreply','ReminderController@reminderAutoReply')->name('reminderautoreply');
	Route::post('addreminderautoreply','ReminderController@addReminderAutoReply')->name('addreminderautoreply');

	/* Templates 
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
*/
	/* Senders */
	Route::post('addsender','SenderController@addSender')->middleware('wanumber')->name('addsender');

	/* CKEditor */
	Route::get('ckbrowse', 'CKController@ck_browse')->name('ckbrowse');
	Route::get('ckdelete', 'CKController@ck_delete_image')->name('ckdelete');
	Route::post('ckupload', 'CKController@ck_upload_image')->name('ckupload');
});

/* Customers */
// Route::post('customer/add','CustomerController@addCustomer')->middleware('customer')->name('addcustomer');
//Route::post('customer/add','CustomerController@addCustomer')->name('addcustomer');
Route::post('subscriber/save','CustomerController@saveSubscriber')->middleware('customer')->name('savesubscriber');
Route::get('test-send-message','CustomerController@testSendMessage');
Route::get('link/activate/{list_name}/{customer_id}','CustomerController@link_activate');
Route::get('link/unsubscribe/{list_name}/{customer_id}','CustomerController@link_unsubscribe');

/* SUBSCRIBE */
Route::get('countries', 'CustomerController@Country');
Route::get('/{list_name}', 'CustomerController@subscriber');
//Route::get('/ev/{list_name}','CustomerController@event'); //register-customer.blade
//Route::get('/{list_name}','CustomerController@index'); //register-customer.blade
