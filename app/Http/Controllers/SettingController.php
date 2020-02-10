<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;
use App\PhoneNumber;

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
  
    public function connect_phone(Request $request)
    {
      $user = Auth::user();
      //cek phone number uda ada didatabase ngga 
      $phoneNumber = PhoneNumber::
                      where("phone_number",$request->phone_number)
                      // ->where("status",2)
                      ->first();
      if (!is_null($phoneNumber)){
        if ($phoneNumber->status == 2) {
          $arr['status'] = 'error';
          $arr['message'] = "Phone Number Already Registered";
          return $arr;
        }
      }

      //cek phone number valid or ngga 
      $request->phone_number = "62".$request->phone_number;
      $is_error = false;
      $error_message = "";
      if(!is_numeric($request->phone_number)){
        $is_error = true;
        $error_message = "Phone number must be a number";
      }
      if(!preg_match("/^628+[0-9]/i",$request->phone_number)){
        $is_error = true;
        $error_message = "Phone number is not valid";
      }
      if ($is_error) {
        $arr['status'] = 'error';
        $arr['message'] = $error_message;
        return $arr;
      }

      
      if (is_null($phoneNumber)){
        $phoneNumber = new PhoneNumber();
        $phoneNumber->user_id = $user->id;
        $phoneNumber->phone_number = $request->phone_number;
        $phoneNumber->counter = 0;
        $phoneNumber->status = 0;
        $phoneNumber->save();
      }
      
      $curl = curl_init();
      $data = array(
          'token'=> '0698a365aec87be50795ab07230d7df55df6eda532b81',
          'phone_number' => $phoneNumber->phone_number,
          'listname'=>'tdlib'.$phoneNumber->id,
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
      
      $phoneNumber->status = 1;
      $phoneNumber->save();

      $arr['status'] = 'Success';
      $arr['message'] = "Please Check your Telegram for Verification Code";
      return $arr;
    }
    
    public function verify_phone(Request $request)
    {
      $arr['status'] = 'Success';
      $arr['message'] = "Telegram Phone number registered";
      return $arr;
    }

/* end class HomeController */
}
