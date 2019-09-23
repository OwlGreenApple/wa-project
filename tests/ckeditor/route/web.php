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

Route::get('ckeditor', 'CKController@index');
Route::get('ckbrowse', 'CKController@ck_browse')->name('ckbrowse');
Route::get('ckdelete', 'CKController@ck_delete_image')->name('ckdelete');
Route::get('ckupload', 'CKController@ck_upload_image')->name('ckupload');

Route::get('role',[
   'middleware' => 'Role:editor',
   'uses' => 'TestController@index',
]);

Route::get('terminate',[
   'middleware' => 'terminate',
   'uses' => 'ABCController@index',
]);

Route::get('profile', [
   'middleware' => 'auth',
   'uses' => 'UserController@showProfile'
]);

Route::get('/usercontroller/path',array(
   'middleware' => 'First',
   'uses' => 'UserController@showPath'
));

Route::resource('my','MyController');

class MyClass{
   public $foo = 'bar';
}
Route::get('/myclass','ImplicitController@index');

Route::get('/foo/bar','UriController@index');

Route::get('/register',function() {
   return view('register');
});
Route::post('/user/register',array('uses'=>'UserRegistration@postRegister'));

Route::get('/cookie/set','CookieController@setCookies');
Route::get('/cookie/get','CookieController@getCookie');

Route::get('/basic_response', function () {
   return 'Hello World';
});

Route::get('/header',function() {
   return response("Hello", 200)->header('Content-Type', 'text/html');
});

Route::get('/cookie',function() {
   return response("Hello", 200)->header('Content-Type', 'text/html')
      ->withcookie('name','Virat Gandhi');
});

Route::get('json',function() {
   return response()->json(['name' => 'Virat Gandhi', 'state' => 'Gujarat']);
});

Route::get('/test2', function() {
   return view('test2');
});

Route::get('/mb', function() {
   return view('master');
});

//Route::get('form','FormController@create')->name(form.create);
//Route::get('form','FormController@store')->name(form.store);