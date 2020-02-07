<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;
use App\PhoneNumbers;

class SettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function connect_phone()
    {
      //cek phone number uda ada didatabase ngga 
      
      //cek phone number valid or ngga 
      
      $curl = curl_init();
      $data = array(
          'token'=> '0698a365aec87be50795ab07230d7df55df6eda532b81',
          'phone_number'=>'+628123238793',
          'authcode'=>'68677',
          // 'phone_number'=>'+6287723238793',
          // 'phone_number'=>'+6287855915535',
          
          'listname'=>'tdlib',
          // tdlib tdlib2 tdlib3 tdlib5
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/auth-set-phone.php",
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_POST => 1,
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response."\n";
        // print_r($response);
        // return json_decode($response, true);
      }
    }

/* end class HomeController */
}
