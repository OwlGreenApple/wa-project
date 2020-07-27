<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PhoneNumber;
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
use App\Message;

class ApiWPController extends Controller
{
  /*
    checkout-created = untuk order yang dibuat
    checkout-completed = untuk order yang selesai
    reminder = untuk pengingat  
  */
    public function send_message_queue_system_WP_activtemplate(Request $request)
    {
      if ($request->key == "wpcallbackforwa" ) {
				$str = $request->phone;
        $phone_number = $request->phone;
        
				if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

        if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }
        
        if ($request->event == "checkout-completed"){
          //list khusus activtemplate
          $list = UserList::where('name',"3ha6ierm")->first();

          if (!is_null($list)) {
            $customer_phone = Customer::where([['list_id',$list->id],['telegram_number',$phone_number]])->first();

            if(is_null($customer_phone))
            {
              $customer = new Customer ;
              $customer->user_id = $list->user_id;
              $customer->list_id = $list->id;
              $customer->name = $request->name;
              $customer->email = $request->email;
              $customer->telegram_number = $phone_number;
              $customer->is_pay= 0;
              $customer->status = 1;
              $customer->save();
              $customer::create_link_unsubs($customer->id,$list->id);

              $customerController = new CustomerController;
              if ($list->is_secure) {
                $ret = $customerController->sendListSecure($list->id,$customer->id,$request->name,$customer->user_id,$list->name,$phone_number);
              }
              $saveSubscriber = $customerController->addSubscriber($list->id,$customer->id,$customer->created_at,$customer->user_id);
            }
          }
        }
        
        $message_send = Message::create_message($phone_number,$request->content,env('REMINDER_PHONE_KEY'));
        
        return "success";
      }
    }
  
    public function send_message_queue_system_WP_celebfans(Request $request)
    {
      if ($request->key == "wpcallbackforwa" ) {
				$str = $request->phone;
        $phone_number = $request->phone;
        
				if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

        if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }
        
        if ($request->event == "checkout-completed"){
          //list khusus activtemplate https://activrespon.com/dashboard/a1yqnefs
          $list = UserList::where('name',"a1yqnefs")->first();

          if (!is_null($list)) {
            $customer_phone = Customer::where([['list_id',$list->id],['telegram_number',$phone_number]])->first();

            if(is_null($customer_phone))
            {
              $customer = new Customer ;
              $customer->user_id = $list->user_id;
              $customer->list_id = $list->id;
              $customer->name = $request->name;
              $customer->email = $request->email;
              $customer->telegram_number = $phone_number;
              $customer->is_pay= 0;
              $customer->status = 1;
              $customer->save();
              $customer::create_link_unsubs($customer->id,$list->id);

              $customerController = new CustomerController;
              if ($list->is_secure) {
                $ret = $customerController->sendListSecure($list->id,$customer->id,$request->name,$customer->user_id,$list->name,$phone_number);
              }
              $saveSubscriber = $customerController->addSubscriber($list->id,$customer->id,$customer->created_at,$customer->user_id);
            }
          }
        }
        
        $message_send = Message::create_message($phone_number,$request->content,env('REMINDER_PHONE_KEY'));
        
        return "success";
      }
    }
  
    public function send_message_queue_system_WP_activflash(Request $request)
    {
      if ($request->key == "wpcallbackforwa" ) {
				$str = $request->phone;
        $phone_number = $request->phone;
        
				if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

        if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }
        
        if ($request->event == "checkout-completed"){
          //list khusus activtemplate https://activrespon.com/dashboard/a1yqnefs
          $list = UserList::where('name',"jsavpwkm")->first();

          if (!is_null($list)) {
            $customer_phone = Customer::where([['list_id',$list->id],['telegram_number',$phone_number]])->first();

            if(is_null($customer_phone))
            {
              $customer = new Customer ;
              $customer->user_id = $list->user_id;
              $customer->list_id = $list->id;
              $customer->name = $request->name;
              $customer->email = $request->email;
              $customer->telegram_number = $phone_number;
              $customer->is_pay= 0;
              $customer->status = 1;
              $customer->save();
              $customer::create_link_unsubs($customer->id,$list->id);

              $customerController = new CustomerController;
              if ($list->is_secure) {
                $ret = $customerController->sendListSecure($list->id,$customer->id,$request->name,$customer->user_id,$list->name,$phone_number);
              }
              $saveSubscriber = $customerController->addSubscriber($list->id,$customer->id,$customer->created_at,$customer->user_id);
            }
          }
        }
        
        $message_send = Message::create_message($phone_number,$request->content,env('REMINDER_PHONE_KEY'));
        
        return "success";
      }
    }
  
    public function send_message_queue_system_WP_digimaru(Request $request)
    {
      if ($request->key == "wpcallbackforwa" ) {
				$str = $request->phone;
        $phone_number = $request->phone;
        
				if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

        if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }
        
        if ($request->event == "checkout-completed"){
          //list khusus digimaru
          $list = UserList::where('name',"iznq923b")->first();

          if (!is_null($list)) {
            $customer_phone = Customer::where([['list_id',$list->id],['telegram_number',$phone_number]])->first();

            if(is_null($customer_phone))
            {
              $customer = new Customer ;
              $customer->user_id = $list->user_id;
              $customer->list_id = $list->id;
              $customer->name = $request->name;
              $customer->email = $request->email;
              $customer->telegram_number = $phone_number;
              $customer->is_pay= 0;
              $customer->status = 1;
              $customer->save();
              $customer::create_link_unsubs($customer->id,$list->id);

              $customerController = new CustomerController;
              if ($list->is_secure) {
                $ret = $customerController->sendListSecure($list->id,$customer->id,$request->name,$customer->user_id,$list->name,$phone_number);
              }
              $saveSubscriber = $customerController->addSubscriber($list->id,$customer->id,$customer->created_at,$customer->user_id);
            }
          }
        }

        $phoneNumber = PhoneNumber::where('user_id',$list->user_id)->first();
        $key = env('REMINDER_PHONE_KEY');
        $mode = 0;
        if (!is_null($phoneNumber)){
          if ($phoneNumber->mode == 0){ //simi
            $server = Server::where('phone_id',$phoneNumber->id)->first();
            $key = $server->url;
            $mode = 0;
          }
          if ($phoneNumber->mode == 1){ //woowa
            $key = $phoneNumber->filename;
            $mode = 1;
          }
        }
        $message_send = Message::create_message($phone_number,$request->content,$key,$mode);

        return "success";
      }
    }
  
    public function send_message_queue_system_WP_ms(Request $request)
    {
      if ($request->key == "wpcallbackforwa" ) {
				$str = $request->phone;
        $phone_number = $request->phone;
        
				if(preg_match('/^62[0-9]*$/',$str)){
          $phone_number = '+'.$str;
        }

        if(preg_match('/^0[0-9]*$/',$str)){
          $phone_number = preg_replace("/^0/", "+62", $str);
        }

        if(preg_match('/^[^62][0-9]*$/',$str)){
          $phone_number = preg_replace("/^[0-9]/", "+62", $str);
        }
        
        if ($request->event == "checkout-completed"){
          //list khusus digimaru
          $list = UserList::where('name',"iznq923b")->first();

          if (!is_null($list)) {
            $customer_phone = Customer::where([['list_id',$list->id],['telegram_number',$phone_number]])->first();

            if(is_null($customer_phone))
            {
              $customer = new Customer ;
              $customer->user_id = $list->user_id;
              $customer->list_id = $list->id;
              $customer->name = $request->name;
              $customer->email = $request->email;
              $customer->telegram_number = $phone_number;
              $customer->is_pay= 0;
              $customer->status = 1;
              $customer->save();
              $customer::create_link_unsubs($customer->id,$list->id);

              $customerController = new CustomerController;
              if ($list->is_secure) {
                $ret = $customerController->sendListSecure($list->id,$customer->id,$request->name,$customer->user_id,$list->name,$phone_number);
              }
              $saveSubscriber = $customerController->addSubscriber($list->id,$customer->id,$customer->created_at,$customer->user_id);
            }
          }
        }
        
        $message_send = Message::create_message($phone_number,$request->content,env('REMINDER_PHONE_KEY'));
        
        return "success";
      }
    }
  
/* end class */    
}
