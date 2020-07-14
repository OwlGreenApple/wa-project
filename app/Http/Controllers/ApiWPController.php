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
use App\Message;

class ApiWPController extends Controller
{
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
        
        $message_send = Message::create_message($phone_number,$request->content,env('REMINDER_PHONE_KEY'));
        
        return "success";
      }
    }
  
/* end class */    
}
