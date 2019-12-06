<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserList;
use App\Customer;
use App\Reminder;
use App\ReminderCustomers;
use Carbon\Carbon;
use App\Sender;

class ApiController extends Controller
{
    public function testapi()
    {
    	$curl = curl_init();

        $data = array(
            'list_id'=> 18,
            'wa_no'=>62895342472008,
            'name'=>'testomnifluencer',
            'email'=>'test@mail.com'
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

        # if customer successful sign up 
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

                 if($validday >= $createdreminder){
                    $reminder_customer = new ReminderCustomers;
                    $reminder_customer->user_id = $row->user_id;
                    $reminder_customer->list_id = $row->list_id;
                    $reminder_customer->sender_id = $sender->id;
                    $reminder_customer->reminder_id = $row->id;
                    $reminder_customer->customer_id = $customerid;
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

    public function generatecoupon($email,$url)
    {
        //https://omnifluencer.com/generate-coupon
        $curl = curl_init();
        $data = array(
            'email'=>$email
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
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
          return $response;
        }
    }

/* end class */    
}
