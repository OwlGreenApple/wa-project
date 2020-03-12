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
use App\Helpers\ApiHelper;
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
      $resend = $request->resend;

      //pastikan phone number hanya 1 phone number
      $countphoneNumber = PhoneNumber::where("user_id",$user->id)->first();
      if(!is_null($countphoneNumber) && $resend == null){
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

      if (!is_null($phoneNumber) && $phoneNumber->status == 2){
          $arr['status'] = 'error';
          $arr['message'] = "Phone Number Already Registered";
          return $arr;
      }

      $member = User::find($user->id);
      $registered_phone = ApiHelper::reg($request->phone_number,$member->name);
      $status_register = json_decode($registered_phone,true);
      $message = strval($status_register['message']);

      if(stripos($message,'success') === false)
      {
          $arr['status'] = 'error';
          $arr['message'] = 'Phone '.$status_register['message'];
          return $arr;
      }

      if(is_null($phoneNumber)){
        $phoneNumber = new PhoneNumber();
        $phoneNumber->user_id = $user->id;
        $phoneNumber->phone_number = $request->phone_number;
        $phoneNumber->counter = 0;
        $phoneNumber->status = 0;
        $phoneNumber->filename = $this->getToken($request->phone_number);
        $phoneNumber->save();
      }

      $arr['status'] = 'success';
      $arr['message'] = "Please open your whatsapp then scan the barcode";
      return $arr;
    }

    private function getToken($no_wa)
    {
        $url='https://116.203.92.59/api/get_ip_key';
        $key='fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89';
        $data = array(
          "no_wa" => $no_wa,
          "key"=>$key,
        );

        $data_string = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 360);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
        );

        return curl_exec($ch);
        //echo $res=curl_exec($ch);
    }

    // DELETE PHONE FROM WOO WA
    public function delete_phone_api($no_wa)
    {
        return ApiHelper::unreg($no_wa);
    }

    public function get_all_client()
    {
        return ApiHelper::get_client();
    }
    
    public function verify_phone(Request $request)
    {
      //SCAN QR CODE
      return ApiHelper::get_qr_code($request->phone_number);
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
