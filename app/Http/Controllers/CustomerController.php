<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Customer;
use App\UserList;
use Carbon\Carbon;
use App\Reminder;
use App\ReminderCustomers;
use App\Sender;
use App\Console\Commands\SendWA as SendMessage;

class CustomerController extends Controller
{
    //Reminder
    public function index(Request $request, $product_list){
    	$check_link = UserList::where([
            ['name','=',$product_list],
            ['is_event','=',0],
            ['status','=',1],
        ])->first();

    	if(empty($product_list)){
    		return redirect('/');
    	} elseif(is_null($check_link)) {
    		return redirect('/');
    	} else {
            $list = UserList::where('name',$product_list)->first();
    		return view('register-customer',['content'=>$list->content,'listname'=>$product_list,'pixel'=>$list->pixel_text,'message'=>$list->message_text]);
    	}
    }

    //Event
    public function event(Request $request, $product_list){
        $check_link = UserList::where([
            ['name','=',$product_list],
            ['is_event','=',1],
            ['status','=',1],
        ])->first();

        if(empty($product_list)){
            return redirect('/');
        } elseif(is_null($check_link)) {
            return redirect('/');
        } else {
            //$request->session()->flash('userlist',$product_list);
            $list = UserList::where('name',$product_list)->first();
            return view('register-customer',['content'=>$list->content, 'listname'=>$product_list,'pixel'=>$list->pixel_text,'message'=>$list->message_text]);
        }
    }

    public function addCustomer(Request $request){
        $listname = $request->listname;
    	$get_id_list = UserList::where('name','=',$listname)->first();
        $wa_number = '+62'.$request->wa_number;
        $sender = Sender::where('user_id',$get_id_list->user_id)->first();
        $today = Carbon::now();
        $wassenger = null;
        $evautoreply = false;
        $valid_customer = false;
        $is_event = $get_id_list->is_event;
        #message & pixel
        $list_message = $get_id_list->message_text;
        $list_wa_number = $get_id_list->wa_number;

        # Filter to avoid unavailable link 
        if(is_null($get_id_list)){
            return redirect('/');
        } else {
            $customer = new Customer;
            $customer->user_id = $get_id_list->user_id;
            $customer->list_id = $get_id_list->id;
            $customer->name = $request->name;
            $customer->wa_number = $wa_number;
            $customer->save();
            $customer_subscribe_date = $customer->created_at;
            $customerid = $customer->id;
        }

        # if customer successful sign up 
    	if($customer->save() == true){
             $valid_customer = true;
    	} else {
    		$data['success'] = false;
    		$data['message'] = 'Error-001! Sorry there is something wrong with our system';
            return response()->json($data);
    	}

        if($is_event == 1 && $valid_customer == true){
            // Event
            $reminder = Reminder::where([
                ['reminders.list_id','=',$get_id_list->id],
                ['lists.is_event','=',1],
                ['reminders.hour_time','<>',null],
                ['reminders.status','=',1],
                ])
                ->leftJoin('lists','reminders.list_id','=','lists.id')
                ->select('reminders.*','lists.event_date')
                ->get();
        } 
        else if($is_event == 0 && $valid_customer == true) {
            // Reminder
             $reminder = Reminder::where([
                ['reminders.list_id','=',$get_id_list->id],
                ['lists.is_event','=',0],
                ['reminders.days','>',0],
                ['reminders.hour_time','=',null],
                ['reminders.status','>',0],
                ])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('reminders.*')
                ->get(); 
        }
        
        if($reminder->count() > 0 && $is_event == 1)
        {
           //Event
            foreach($reminder as $row)
            {

                 $today_event = Carbon::now()->toDateString();
                 $days = (int)$row->days;
                 $event_date = Carbon::parse($row->event_date);

                 if($days < 0){
                   $days = abs($days);
                   $event_date->subDays($days);
                 } else {
                   $event_date->addDays($days);
                 }

                 if($event_date >= $today_event){
                    $reminder_customer = new ReminderCustomers;
                    $reminder_customer->user_id = $row->user_id;
                    $reminder_customer->list_id = $row->list_id;
                    $reminder_customer->sender_id = $sender->id;
                    $reminder_customer->reminder_id = $row->id;
                    $reminder_customer->customer_id = $customerid;
                    $reminder_customer->save();
                    $eligible = true;
                 } else {
                    $eligible = false;
                 }
            }

             if($reminder_customer->save() == true && !empty($eligible)){
                 return $this->autoReply($get_id_list->id,$wa_number,$list_message,$list_wa_number,$request->name);
             } else {
                 $data['success'] = false;
                 $data['message'] = 'Error-001! Sorry there is something wrong with our system';
                 return response()->json($data);
             }
        } else if($reminder->count() > 0 && $is_event == 0) 
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
                    $eligible = true;
                 }
            }

             # if reminder has been set up into reminder-customer 
            if($reminder_customer->save() == true && !empty($eligible)){
                return $this->autoReply($get_id_list->id,$wa_number,$list_message,$list_wa_number,$request->name);
            } else {
                $data['success'] = false;
                $data['message'] = 'Error-002! Sorry there is something wrong with our system';
                return response()->json($data);
            }    
        } else {
            return $this->autoReply($get_id_list->id,$wa_number,$list_message,$list_wa_number,$request->name);
        }

    }    
         
   public function autoReply($listid,$wa_number,$list_message,$list_wa_number,$customer_name){
        #send wa link to send message to list owner
        $list_wa_number = str_replace("+","",$list_wa_number);
        $data['wa_link'] = 'https://api.whatsapp.com/send?phone='.$list_wa_number.'&text='.$list_message.'';

        # Sending event auto reply for customer, return true if user has not set auto reply yet
        $autoreply = Reminder::where([
                ['reminders.list_id','=',$listid],
                ['reminders.days','=',0],
                ['reminders.hour_time','=',null],
                ['reminders.status','=',1],
                ])->join('lists','reminders.list_id','=','lists.id')
                ->select('reminders.*')->first();

        if(is_null($autoreply)){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } else {
             # wassenger
            $user_id = $autoreply->user_id;
            $getsender = Sender::where('user_id',$user_id)->first();

            $message = str_replace('{name}',$customer_name,$autoreply->message);
            $status = $autoreply->status;
        }

        if($status == 1){
            $sendmessage = new SendMessage;
            $wasengger = $sendmessage->sendWA($wa_number,$message);
        } else {
            $wasengger = null;
        }

        # if status from reminder has set to 0 or disabled
        if($wasengger == null && $status > 1){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } else if($wasengger !== null && $status == 1){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } else {
            $data['success'] = false;
            $data['message'] = 'Error-WAS! Sorry there is something wrong with our system';
            return response()->json($data);
        }
    }

/* end of class */
}
