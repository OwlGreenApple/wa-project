<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;
use App\PhoneNumber;
use App\Rules\TelNumber;
use App\Rules\AvailablePhoneNumber;

use DB;

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
  
    public function index()
    {
      $user = Auth::user();
      return view('auth.settings',['user'=>$user]);
    }

    public function settingsUser(Request $request)
    {
        $id = Auth::id();
        $data = array(
            'name'=> $request->user_name,
            'phone_number'=>$request->user_phone,
        );

        if(!empty($request->oldpass) && !empty($request->confpass) && !empty($request->newpass))
        {
            $data['password']= Hash::make($request->newpass);
        }

        try{
          User::where('id',$id)->update($data);
          $error['status'] = 'success';
          $error['message'] = 'Your data has been updated successfully';
        }catch(Exception $e){
          $error['status'] = 'failed';
          $error['message'] = 'Sorry, failed to update data, please contact admin';
        }

        return response()->json($error);
    }
    
    public function load_phone_number()
    {
      $user = Auth::user();
      $phoneNumbers = PhoneNumber::
                      where("user_id",$user->id)
                      ->get();
      $arr['view'] =(string) view('auth.setting-phone-numbers')
                      ->with([
                        "phoneNumbers"=>$phoneNumbers,
                      ]);

      return $arr;
    }

    public function editPhone(Request $request)
    {
      $user = Auth::id();
      $phone_number = $request->edit_phone;

      $rules = [
        'edit_phone' =>['required','min:9','max:18',new TelNumber, new AvailablePhoneNumber]
      ];

      $validator = Validator::make($request->all(),$rules);

      if($validator->fails())
      {
        $error = $validator->errors();
        $err['error'] = 'true';
        $err['message'] = $error->first('edit_phone');
        return response()->json($err);
      }

      try{
        PhoneNumber::where('user_id',$user)->update(['phone_number'=>$phone_number,'status'=>0]);
        $update = true;
      }catch(Exception $e){
        $data['message'] = 'Error! Sorry cannot edit your phone, please contact admin';
        return response()->json($data);
      }

      if($update == true)
      {
         $server = DB::table('phone_numbers')->select(DB::raw('SUBSTRING(filename, -1) AS filename'))->where('user_id',$user)->first();
         $idserver = $server->filename;

         if($idserver == '')
         {
            $filename = env('FILENAME_API').'0';
         }
         else {
            $serverint = (int)$server->filename + 1;
            $filename = env('FILENAME_API').$serverint;
         }
         
        //CONNECT PHONE NUMBERS
        $this->ChatTelegramNumber($phone_number,$filename);

        try{
          PhoneNumber::where('user_id',$user)->update(['filename'=>$filename,'status'=>1]);
          $data['status'] = 'success';
          $data['message'] = "Your phone number has been edited, please Check your Telegram for Verification Code";
        }catch(Exception $e){
          $data['status'] = 'error';
          $data['message'] = 'Error! Sorry cannot edit your phone, please contact admin';
        }
      }
      $data['phone'] = $phone_number;
      return response()->json($data);
    }

    public function ChatTelegramNumber($phone_number,$filename)
    {
      $curl = curl_init();
      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phone_number,
          'filename'=>$filename,
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
        // echo "cURL Error #:" . $err;
        $arr['status'] = 'error';
        $arr['message'] = "Please try to connect again". $err;
        return $arr;
      } else {
        // echo $response."\n";
      }
    }
    
    public function connect_phone(Request $request)
    {
      $user = Auth::user();

      //pastikan phone number hanya 1 phone number
      $countphoneNumber = PhoneNumber::where("user_id",$user->id)->first();
      if(!is_null($countphoneNumber)){
          $arr['status'] = 'error';
          $arr['message'] = "Sorry, you can create 1 phone number only";
          return $arr;
      }

      //cek phone number uda ada didatabase ngga 
      $phoneNumber = PhoneNumber::
                      where("phone_number",$request->phone_number)
                      ->where("user_id",$user->id)
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
      /*$request->phone_number = "62".$request->phone_number;
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
      }*/


      if (is_null($phoneNumber)){
        $phoneNumber = new PhoneNumber();
        $phoneNumber->user_id = $user->id;
        $phoneNumber->phone_number = $request->phone_number;
        $phoneNumber->counter = 0;
        $phoneNumber->status = 0;
        $statement = DB::select("show table status like 'phone_numbers' ");
        $phoneNumber->filename = env('FILENAME_API').$statement[0]->Auto_increment;
        $phoneNumber->save();
      }

      $server = DB::table('phone_numbers')->select(DB::raw('SUBSTRING(filename, -1) AS filename'))->where('user_id',Auth::id())->first();
         $idserver = $server->filename;

       if($idserver == '')
       {
          $filename = env('FILENAME_API').'0';
       }
       else {
          $serverint = (int)$server->filename + 1;
          $filename = env('FILENAME_API').$serverint;
       }

      $curl = curl_init();
      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phoneNumber->phone_number,
          'filename'=>$filename,
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
        // echo "cURL Error #:" . $err;
        $arr['status'] = 'error';
        $arr['message'] = "Please try to connect again". $err;
        return $arr;
      } else {
        // echo $response."\n";
      }
      
      $phoneNumber->status = 1;
      $phoneNumber->save();

      $arr['status'] = 'success';
      $arr['message'] = "Please Check your Telegram for Verification Code";
      return $arr;
    }
    
    public function verify_phone(Request $request)
    {
      $user = Auth::user();
      //cek phone number uda ada didatabase ngga 
      $phoneNumber = PhoneNumber::
                      where("phone_number",$request->phone_number)
                      ->where("user_id",$user->id)
                      ->first();
      if (!is_null($phoneNumber)){
        if ($phoneNumber->status == 2) {
          $arr['status'] = 'error';
          $arr['message'] = "Phone Number Already Registered";
          return $arr;
        }
      }

      //cek phone number valid or ngga 
      /*$request->phone_number = "62".$request->phone_number;
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
      }*/


      $curl = curl_init();
      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phoneNumber->phone_number,
          'filename'=>$phoneNumber->filename,
          'authcode'=>$request->verify_code,
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/auth-verify-phone.php",
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_POST => 1,
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        // echo "cURL Error #:" . $err;
        $arr['status'] = 'error';
        $arr['message'] = "Please try to connect again";
        return $arr;
      } else {
        // echo $response."\n";
        print_r($response);exit;
        // return json_decode($response, true);
      }
      
      $phoneNumber->status = 2;
      $phoneNumber->save();

      $arr['status'] = 'success';
      $arr['message'] = "Telegram Phone number registered";
      return $arr;
    }

    public function delete_phone(Request $request)
    {
      $phoneNumber = PhoneNumber::find($request->id);
      $phoneNumber->delete();
      
      //hapus diAPI juga
      
      $arr['status'] = 'success';
      $arr['message'] = "Telegram Phone number deleted";
      return $arr;
    }
}
