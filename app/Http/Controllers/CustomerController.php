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
    		return view('register-customer',['content'=>$list->content,'listname'=>$product_list]);
    	}
    }

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
            return view('register-customer',['content'=>$list->content, 'listname'=>$product_list]);
        }
    }

    public function addCustomer(Request $request){
        $listname = $request->listname;
    	$get_id_list = UserList::where('name','=',$listname)->first();
        $wa_number = '+62'.$request->wa_number;
        $sender = Sender::where('user_id',$get_id_list->user_id)->first();
        $wassenger = null;
        $evautoreply = false;

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
        }

        # if customer successful sign up 
    	if($customer->save() == true){
            // Auto Reply Event
            $autoreply = Reminder::where([
                ['reminders.list_id','=',$get_id_list->id],
                ['reminders.days','=',0],
                ['reminders.hour_time','=',null],
                ])->join('lists','reminders.list_id','=','lists.id')
                ->select('reminders.*')->first();

            // Reminder
    		$reminder_id = Reminder::where([
                ['reminders.list_id','=',$get_id_list->id],
                ['lists.is_event','=',0]])
                ->join('lists','reminders.list_id','=','lists.id')
                ->max('reminders.id');

            $reminder = Reminder::where([['id','=',$reminder_id],['status',1]])->first();
    	} else {
    		$data['success'] = false;
    		$data['message'] = 'Error-001! Sorry there is something wrong with our system';
    	}

        # Sending event auto reply for customer, return true if user has not set auto reply yet
        if(is_null($autoreply)){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } else {
             # wassenger
            $user_id = $autoreply->user_id;
            $getsender = Sender::where('user_id',$user_id)->first();

            $message = $autoreply->message;
            $status = $autoreply->status;
        }

        if($status == 1){
            $sendmessage = new SendMessage;
            $wasengger = $sendmessage->sendWA($wa_number,$message);
        } else {
            $wasengger = null;
        }

        # if status from event has set to 0 or disabled
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

        # sending message / reminder 
        if(is_null($reminder)){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } else {
            $reminder_customer = new ReminderCustomers;
            $reminder_customer->user_id = $reminder->user_id;
            $reminder_customer->list_id = $reminder->list_id;
            $reminder_customer->sender_id = $sender->id;
            $reminder_customer->reminder_id = $reminder_id;
            $reminder_customer->customer_id = $customer->id;
            $reminder_customer->save();
        }

        # if reminder has been set up into reminder-customer 
        if($reminder_customer->save() == true){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
        } else {
            $data['success'] = false;
            $data['message'] = 'Error-002! Sorry there is something wrong with our system';
        }
    	return response()->json($data);
    }

}
