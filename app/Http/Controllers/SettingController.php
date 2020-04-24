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
use App\Order;
use App\PhoneNumber;
use App\Rules\TelNumber;
use App\Rules\AvailablePhoneNumber;
use App\Helpers\ApiHelper;
use App\Helpers\Alert;
use DB;
use Carbon\Carbon;
use DateTimeZone;

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
      $day_left = User::find($user->id)->day_left;
      $expired = Carbon::now()->addDays($day_left)->toDateString();
      $mod = request()->get('mod');

      $is_registered = 0;
      $phoneNumber = PhoneNumber::
                      where("user_id",$user->id)
                      ->first();
      if (!is_null($phoneNumber)) {
        $is_registered = 1;
      }

      ($user->timezone == null)?$user_timezone = 'Asia/Jakarta':$user_timezone = $user->timezone;

      return view('auth.settings',[
        'user'=>$user,
        'is_registered'=>$is_registered,
        'timezone'=>$this->showTimeZone(),
        'expired'=>Date('d M Y',strtotime($expired)),
        'user_timezone'=>$user_timezone,
        'mod'=>$mod
      ]);
    }

    public function showTimeZone(){
      $timezone = array();
      $timestamp = time();
      
      foreach(timezone_identifiers_list(DateTimeZone::ALL) as $key => $t) {
          date_default_timezone_set($t);
          $timezone[$key]['zone'] = $t;
          $timezone[$key]['GMT_difference'] =  date('P', $timestamp);
      }
      $timezone = collect($timezone)->sortBy('GMT_difference');

      return $timezone;
    }

    public function settingsUser(Request $request)
    {
        $id = Auth::id();
        $data = array(
            'name'=> $request->user_name,
            'phone_number'=>$request->user_phone,
            'timezone'=>$request->timezone,
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

      //CHECK WHETHER PHONE IS CONNECTED OR NOT
      if($phoneNumbers->count() > 0)
      {
        foreach($phoneNumbers as $rows)
        {
            $phone_number = $rows->phone_number;
            $check_qr_status = ApiHelper::qr_status($phone_number);
            $phone_string = substr($phone_number, 1);

            // if($check_qr_status <> $phone_string)
            // {
                // PhoneNumber::where([["user_id",$user->id],['phone_number',$phone_number]])->update(['status'=>0]);
            // }
        }
      }

      $phone_updated = PhoneNumber::where("user_id",$user->id)->get();
      $arr['view'] =(string) view('auth.setting-phone-numbers')
                      ->with([
                        "phoneNumbers"=>$phone_updated,
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

    public function connect_phone(Request $request)
    {
      $user = Auth::user();

      if($this->checkIsPay() == 0)
      {
          $arr['status'] = 'error';
          $arr['message'] = 'Currently you don\'t have any package left, Please Order new package now.';
          return $arr;
      }

      $resend = $request->resend;
      $phone_number = $request->code_country.$request->phone_number;

      //pastikan phone number hanya 1 phone number
      $countphoneNumber = PhoneNumber::where("user_id",$user->id)->first();
      if(!is_null($countphoneNumber) && $resend == null){
          $arr['status'] = 'error';
          $arr['message'] = Alert::one_number();
          return $arr;
      }

      //cek phone number uda ada didatabase ngga 
      $phoneNumber = PhoneNumber::
                      where("phone_number",$phone_number)
                      ->where("user_id",$user->id)
                      // ->where("status",2)
                      ->first();

      if (!is_null($phoneNumber) && $phoneNumber->status == 2){
          $arr['status'] = 'error';
          $arr['message'] = Alert::exists_phone();
          return $arr;
      }

      //new system, didelete dulu baru dieksekusi
      ApiHelper::unreg($phone_number);

      //PHONE REGISTER TO API
      $registered_phone = ApiHelper::reg($phone_number,$user->name);
      $status_register = json_decode($registered_phone,true);
      $message = strval($status_register['message']);

      /* diremark karena dianggap selalu success
      if(stripos($message,'success') === false)
      {
          $arr['status'] = 'error';
          $arr['message'] = 'Phone '.$status_register['message'];
          return $arr;
      }
      */

      /*new system if(is_null($phoneNumber)){
        // $token = explode(':',$this->getToken($request->phone_number));
        $phoneNumber = new PhoneNumber();
        $phoneNumber->user_id = $user->id;
        $phoneNumber->phone_number = $phone_number;
        $phoneNumber->counter = 0;
        $phoneNumber->status = 0;
        // $phoneNumber->filename = $token[1];
        $phoneNumber->filename = "";
        $phoneNumber->save();
      }*/

      $arr['status'] = 'success';
      $arr['message'] = Alert::connect_success();
      return $arr;
    }

    /*
    * GET QR CODE
    */
    public function verify_phone(Request $request)
    {
      $user = Auth::user();
      /*new system $phoneNumber = PhoneNumber::
                      where("phone_number",$request->phone_number)
                      ->where("user_id",$user->id)
                      ->first();*/
                      
      /*
      Cek database, klo status masi 0 maka akan request ke woowa 
      Cek Ready or not (after 3-5 min register phone no)
      */
      /*new system if ($phoneNumber->status == 0) {*/
        $arr = json_decode(ApiHelper::status_nomor($request->phone_number),1);
        if (!is_null($arr)) {
          if($arr['status']=="success"){
            /*new system $phoneNumber->status=1;
            $phoneNumber->save();*/
          }
        }
        else {
          $error = array(
            'status'=>'error',
            'phone_number'=>Alert::error_verify(),
          );
          return response()->json($error);
        }
      //}
      
      /*new system if ($phoneNumber->status == 1) {*/
          $qr_code = ApiHelper::get_qr_code($request->phone_number);

          if($qr_code == false)
          {
            $data = array(
              'status'=>'error',
              'phone_number'=>Alert::qrcode(),
            );
          }
          else
          {
            $data = array(
              'status'=>'success',
              'data'=>$qr_code,
            );
          }
          return response()->json($data);
      //}
        
    }

    /*
    * Confirm QR CODE
    */
    public function check_connected_phone(Request $request)
    {
        $user = Auth::user();
        $counter = $this->checkIsPay();
        if($counter == 0)
        {
            $response['status'] = 'Currently you don\'t have any package left, Please Order new package now.';
            return json_encode($response);
        }
        else
        {
            $day_counter = $counter['day'];
            $month_counter = $counter['month'];
        }

        if($request->phone_number <> null)
        {
            $no_wa = $request->phone_number;
        }
        else 
        {
            $no_wa = $request->no_wa;
        }

        $wa_number = substr($no_wa, 1);
        $status_connect = ApiHelper::qr_status($no_wa);
        //if status_connect == none which mean phone still not connect
				if ( ($status_connect == $wa_number) || ($status_connect == "phone_offline")){
            /* new system $data = array(
              'status'=>2,
              'counter'=>6,
              'max_counter'=>5000
            );*/
            $key = $this->get_key($no_wa);
            try{
              // new system PhoneNumber::where([['user_id',$user->id],['phone_number',$no_wa]])->update($data);
              $phoneNumber = new PhoneNumber();
              $phoneNumber->user_id = $user->id;
              $phoneNumber->phone_number = $no_wa;
              $phoneNumber->filename = $key;
              $phoneNumber->counter = env('COUNTER');
              $phoneNumber->max_counter_day = $day_counter;
              $phoneNumber->max_counter = $month_counter;
              $phoneNumber->status = 2;
              $phoneNumber->save();

              $response['status'] = 'Congratulations, your phone is connected';
            }catch(Exception $e){
              $response['status'] = 'Sorry, there is some error, please retry to verify your phone';
            }
        }
        else {
          $response['status'] = $status_connect;
        }
       
        return json_encode($response);
    }

    public function checkIsPay()
    {
      $userid = Auth::id();
      $check_order = User::find($userid);
      if($check_order->membership <> null && $check_order->day_left > 0 && $check_order->status > 0)
      {
          $type_package = substr($check_order->membership,-1,1);
          $counter = Alert::package($type_package);
          return $counter;
      }
      else
      {
         return 0;
      }
    }

    public function delete_phone(Request $request)
    {
      $phoneNumber = PhoneNumber::find($request->id);
      $wa_number = $phoneNumber->phone_number;
      $delete_api = ApiHelper::unreg($wa_number);

      if($delete_api !== "success")
      {
        $phoneNumber->delete();
        $arr['status'] = 'success';
        $arr['message'] = "The phone number has been deleted";
        return $arr;
      }

      try{
        $phoneNumber->delete();
        $arr['status'] = 'success';
        $arr['message'] = "The phone number has been deleted";
      }catch(Exception $e){
        $arr['status'] = 'error';
        $arr['message'] = "Error! Sorry unable to delete your phone number";
      } 

      return $arr;
    }

    public function delete_api($wa_number)
    {
        ApiHelper::unreg($wa_number);
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
    
    public function get_all_cust()
    {
        return ApiHelper::get_all_cust();
    }

    public function qr_status($wa_number)
    {
        $arr = json_decode(ApiHelper::qr_status($wa_number),1);
        if (!is_null($arr)) {
          return $arr['status'];
        } 
        else {
          echo "null";
        }
    }

    public function status_nomor($wa_number)
    {
        $arr = json_decode(ApiHelper::status_nomor($wa_number),1);
        if (!is_null($arr)) {
          return $arr['status'];
        } 
        else {
          echo "null";
        }
    }

    public function get_qr_code($wa_number)
    {
      return ApiHelper::get_qr_code($wa_number);
        $arr = json_decode(ApiHelper::get_qr_code($wa_number),1);
        if (!is_null($arr)) {
          return $arr['status'];
        } 
        else {
          echo "null";
        }
    }

    public function take_screenshot($wa_number)
    {
        return ApiHelper::take_screenshot($wa_number);
    }
    
    public function get_key($wa_number)
    {
        $key = ApiHelper::get_key($wa_number);
        $response = json_decode($key,true);
        $response = explode(':',$response['message']);
        $api_key = $response[1];
        return $api_key;
    }

    public function send_message($wa_number,$message,$key)
    {
        return ApiHelper::send_message($wa_number,$message,$key);
    }
		
    public function send_image_url()
    {
			//6285967284773
        // return ApiHelper::send_image_url($wa_number,$url,$message,$key);
        return ApiHelper::send_image_url("628123238793","https://activrespon.com/dashboard/assets/img/pricing-bg-1.jpg","test message","67fb470ceb5c439b9241d1a65167bd7c6946a47a98cacf15");
        // return ApiHelper::send_image_url("628123238793","https://www.emmasdiary.co.uk/images/default-source/default-album/9-months.jpg?sfvrsn=9dde63ad_0","test message","3a0b718387f65aa92d93df352e30c8d227f018456385a33c");
    }
}
