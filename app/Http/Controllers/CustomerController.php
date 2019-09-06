<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\UserList;
use Carbon\Carbon;
use App\Reminder;
use App\ReminderCustomers;

class CustomerController extends Controller
{
    public function index(Request $request, $product_list){
    	$check_link = UserList::where([
            ['name','=',$product_list],
            ['status','=',1],
        ])->first();

    	if(empty($product_list)){
    		return redirect('/');
    	} elseif(is_null($check_link)) {
    		return redirect('/');
    	} else {
    		$request->session()->flash('userlist',$product_list);
    		return view('register-customer');
    	}
    }

    public function addCustomer(Request $request){
    	$userlist =  $request->session()->get('userlist'); //retrieve session from userlist
        $request->session()->reflash();
    	$get_id_list = UserList::where('name','=',$userlist)->first();

        /* Filter to avoid unavailable link */
        if(is_null($get_id_list)){
            return redirect('/');
        } else {
            $wa_number = $request->code_country.$request->wa_number;
            $customer = new Customer;
            $customer->user_id = $get_id_list->user_id;
            $customer->list_id = $get_id_list->id;
            $customer->name = $request->name;
            $customer->wa_number = $wa_number;
            $customer->save();
        }

        /* if customer successful sign up */
    	if($customer->save() == true){
    		$reminder_id = Reminder::where('list_id','=',$get_id_list->id)->max('id');
            $reminder = Reminder::where([['id','=',$reminder_id],['status',1]])->first();
    	} else {
    		$data['success'] = false;
    		$data['message'] = 'Error-001! Sorry there is something wrong with our system';
    	}

        /* if reminder empty  */
        if(is_null($reminder)){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } else {
            $reminder_customer = new ReminderCustomers;
            $reminder_customer->user_id = $reminder->user_id;
            $reminder_customer->list_id = $reminder->list_id;
            $reminder_customer->reminder_id = $reminder_id;
            $reminder_customer->customer_id = $customer->id;
            $reminder_customer->message = $reminder->message;
            $reminder_customer->save();
        }

        /* if reminder has been set up into reminder-customer */
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
