<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserList;
use App\Customer;
use App\Reminder;
use App\ReminderCustomers;
use Carbon\Carbon;
use App\Sender;
use App\Mail\SendWAEmail;
use App\Console\Commands\SendWA as wamessage;
use Mail;
use App\Http\Controllers\CustomerController;
use App\Helpers\ApiHelper;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    public function test()
    {

      $dt = Carbon::now();
      $date = $dt->format('d-M-Y');

      dd($date);
       /* $phone_number = null;
        $str = '628123238793';

        if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

         if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }

        dd($phone_number);*/
    }

    public function entry_google_form(Request $request)
    {
			$obj = json_decode($request->getContent());

			$list = UserList::where('name',$obj->list_name)->first();

			if (!is_null($list)) {
				$str = $obj->phone_number;

				if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

        if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }

				$customer = new Customer ;
				$customer->user_id = $list->user_id;
				$customer->list_id = $list->id;
				$customer->name = $obj->name;
				$customer->email = $obj->email;
				$customer->telegram_number = $phone_number;
				$customer->is_pay= 0;
				$customer->status = 1;
				$customer->save();

        $customerController = new CustomerController;
				if ($list->is_secure) {
					$ret = $customerController->sendListSecure($list->id,$customer->id,$obj->name,$customer->user_id,$list->name,$phone_number);
				}
        $saveSubscriber = $customerController->addSubscriber($list->id,$customer->id,$customer->created_at,$customer->user_id);
				
			}
    }

    public function send_simi(Request $request)
    {
      return ApiHelper::send_simi($request->customer_phone,$request->message,$request->server_url);
    }
    
    public function send_message(Request $request)
    {
      $obj = json_decode($request->getContent());
      return ApiHelper::send_message($obj->customer_phone,$obj->message,$obj->key_woowa);
    }
    
    public function send_image_url_simi(Request $request)
    {
      Storage::disk('local')->put('temp-send-image-simi/'.$request->image, file_get_contents(Storage::disk('s3')->url($request->image)));
      $send_message = ApiHelper::send_image_url_simi($request->customer_phone,$request->curl,$request->message,$request->server_url);
    }
    
    public function send_image_url(Request $request)
    {
      return ApiHelper::send_image_url($customer_phone,Storage::disk('s3')->url($request->image),$message,$key);
    }
    
    public function testapi()
    {
    	$curl = curl_init();

        $data = array(
            'list_id'=> 17,
            'wa_no'=>11111111111,
            'name'=>'Rizky',
            'email'=>'celebgramme.dev@gmail.com',
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://localhost/waku/private-list",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response."\n";
        }
    }

    public function register_list(Request $request)
    {
    	$data = json_decode($request->getContent(),true);
      $list = UserList::where('id',$data['list_id'])->first();

    	if(is_null($list))
    	{
    	 	$msg['is_error'] = 'Id not available, it may Deleted!!!';
    	 	return $msg;
    	}
        $userid = $list->user_id;
         /**/
        $today = Carbon::now();
        $valid_customer = false;
        $is_event = $list->is_event;
        #message & pixel
        $list_message = $list->message_text;
        $list_wa_number = $list->wa_number;
        $sender = Sender::where([['user_id',$list->user_id],['wa_number','=',$list->wa_number]])->first();
        
        $cust = new Customer;
        $cust->user_id = $userid;
        $cust->list_id = $data['list_id'];
        $cust->name = $data['name'];
        $cust->email = $data['email'];
        $cust->wa_number = $data['wa_no'];
        $cust->save();
        $customer_subscribe_date = $cust->created_at;
        $customerid = $cust->id;

        // if customer successful sign up 
        if($cust->save() == true){
             $valid_customer = true;
        } else {
            $data['success'] = false;
            $data['message'] = 'Error-000! Sorry there is something wrong with our system';
            return response()->json($data);
        }

         $reminder = Reminder::where([
            ['reminders.list_id','=',$data['list_id']],
            ])
            ->join('lists','reminders.list_id','=','lists.id')
            ->select('reminders.*')
            ->get(); 
        
        if($reminder->count() > 0) 
        {
            // Reminder
            foreach($reminder as $row)
            {
                $days = (int)$row->days;
                $after_sum_day = Carbon::parse($customer_subscribe_date)->addDays($days);
                $validday = $after_sum_day->toDateString();
                $createdreminder = Carbon::parse($row->created_at)->toDateString();
                $reminder_status = $row->status;
                ($reminder_status == 1)?$reminder_response = 0 : $reminder_response = 3;

                 if($validday >= $createdreminder){
                    $reminder_customer = new ReminderCustomers;
                    $reminder_customer->user_id = $row->user_id;
                    $reminder_customer->list_id = $row->list_id;
                    $reminder_customer->sender_id = $sender->id;
                    $reminder_customer->reminder_id = $row->id;
                    $reminder_customer->customer_id = $customerid;
                    $reminder_customer->status = $reminder_response;
                    $reminder_customer->save(); 
                    $eligible = true; 
                 } else {
                    $eligible = null;
                 }
            }
  
        } 
        /**/
    	if($eligible == true && $cust->save())
    	{
    	   $msg['is_error'] = 0;
    	}
    	else
    	{
    	   $msg['is_error'] = 1;
    	}
    	return $msg;
    }

    public function testmail()
    {
        //$url = 'http://192.168.88.177/omnifluencer-project/sendmailfromactivwa';
        $url = 'http://192.168.88.177/omnilinkz/sendmailfromactivwa';
        $mail = 'celebgramme.dev@gmail.com';
        $emaildata = 'code_coupon';
        $subject = 'Test coupon code';
        return $this->callMailApi($url,$mail,$emaildata,$subject);
    }

    public function callMailApi($url,$mail,$emaildata,$subject)
    {
        $curl = curl_init();
        $data = array(
            'mail'=>$mail,
            'emaildata'=>$emaildata,
            'subject'=>$subject,
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTREDIR => 3,
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          //echo $response;
          return json_decode($response,true);
        }
    }


    public function testcoupon()
    {
        $email = 'celebgramme.dev@gmail.com';
        $package = 'package-premium-6';
       // $url = 'http://192.168.88.177/omnifluencer-project/generate-coupon';
        $url = 'http://192.168.88.177/omnilinkz/generate-coupon';
        $this->generatecoupon($email,$package,$url);
    }

    public function generatecoupon($email,$package,$url)
    {
        //https://omnifluencer.com/generate-coupon
        $curl = curl_init();
        $data = array(
            'email'=>$email,
            'package'=>$package,
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTREDIR => 3,
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          //echo $response;
          return json_decode($response,true);
        }
    }

    public function customerPay(Request $request)
    {
        $data = json_decode($request->getContent(),true);
        $sql = [
            ['email','=',$data['email']],
            ['list_id','=',$data['list_id']],
        ];
        $check_customer = Customer::where($sql)->first();

        if($data['is_pay'] == 1 && !is_null($check_customer))
        {
            Customer::where($sql)->update(['is_pay'=>$data['is_pay']]);
            $arr['response'] = 1;
        }
        else
        {
            $arr['response'] = 0;
        }
        return response()->json($arr);
    }

    public function testpay()
    {
        $curl = curl_init();
        $data = array(
            'email'=>'celebgramme.dev@gmail.com',
            'list_id'=>17,
            'is_pay'=>1
        );
        $url = 'http://localhost/waku/is_pay';
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTREDIR => 3,
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
          //return json_decode($response,true);
        }
    }

    public function testDirectSendWA(Request $request)
    {
        $uid = 6287852700229;
        $to = $request->to;
        $message = $request->wa_message;

        $karakter= 'abcdefghjklmnpqrstuvwxyz123456789';
        $string = 'testsendwaactivwa-';
        for ($i = 0; $i < 7 ; $i++) {
          $pos = rand(0, strlen($karakter)-1);
          $string .= $karakter{$pos};
        }
        $idmessage = $string;

        $wa = new wamessage;
        $send = $wa->sendWA($uid,$to,$message,$idmessage);

        if(!empty($send['success']))
        {
            $data['msg'] = 'Message sudah dikirim';
        }
        else
        {
            $data['msg'] = 'Message gagal dikirim';
        }

        return response()->json($data);
    } 

    public function testDirectSendMail(Request $request)
    {
        $to = $request->to;
        $message = $request->message;
        $subject = $request->subject;
        
        Mail::to($to)->queue(new SendWAEmail($message,$subject));

        $data['msg'] = 'Message sudah dikirim';
        return response()->json($data);
    }

/* end class */    
}
