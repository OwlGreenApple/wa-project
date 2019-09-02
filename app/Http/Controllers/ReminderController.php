<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\Reminder;
use App\ReminderCustomers;
use App\Customer;
use Carbon\Carbon;

class ReminderController extends Controller
{

	/* Display created reminder */
    public function index(){
    	$id = Auth::id();
    	$list = Reminder::where('reminders.user_id',$id)
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','reminders.*')
    			->get();
    	return view('reminder.reminder',['data'=>$list]);
    }

    /* Display form to create reminder */
    public function reminderForm(){
    	$id = Auth::id();
    	$list = UserList::where('user_id',$id)->get();
    	return view('reminder.reminder-form',['data'=>$list]);
    }

    /* Create and insert data reminder and reminder customer into database */
    public function addReminder(Request $request){
    	$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
    	$days = $req['day'];

    	foreach($req['id'] as $row=>$list_id){
    		$reminder = new Reminder;
    		$reminder->user_id = $user_id;
    		$reminder->list_id = $list_id;
    		$reminder->days = $days;
    		$reminder->message = $message;
    		$reminder->save();
    	}

    	/* If data successfully inserted into reminder */
    	if($reminder->save() == true){
    		foreach($req['id'] as $row=>$list_id){
    			/* retrieve customer id */
    			$customer = Customer::where([
    				['list_id','=',$list_id],
    				['status','=',1],
    			])->get();
    			/* retrieve reminder id according on created at */
    			$created_date = $reminder->created_at;
    			$reminder_get_id = Reminder::where([
    				['list_id','=',$list_id],
    				['created_at','=',$created_date],
    			])->select('id')->get();
    			/* insert into reminder customer */
    			foreach($customer as $col){
                    $customer_signup = Carbon::parse($col->created_at);
                    $adding_day = $customer_signup->addDays($reminder->days);

                    /* if user signup's date after adding >= with reminder date's created */
                    if($adding_day >= $created_date){
                        foreach($reminder_get_id as $id_reminder){
                            $remindercustomer = new ReminderCustomers;
                            $remindercustomer->user_id = $user_id;
                            $remindercustomer->list_id = $list_id;
                            $remindercustomer->reminder_id = $id_reminder->id;
                            $remindercustomer->customer_id = $col->id;
                            $remindercustomer->message = $message;
                            $remindercustomer->save();
                        }
                    }
    			}
	    	}
    	} else {
    		return redirect('reminderform')->with('status_error','Error!! failed to set reminder');
    	}

    	/* If successful insert data into reminder customer */
    	if($remindercustomer->save() == true){
    		return redirect('reminderform')->with('status','Your reminder customer has been set!');
    	} else {
    		return redirect('reminderform')->with('status_error','Error!! failed to set reminder for customer');
    	}
    }

    /* Display reminder customer */
    public function displayReminderCustomers()
    {
    	$id_user = Auth::id();
    	$remindercustomer = ReminderCustomers::where('reminder_customers.user_id','=',$id_user)
    						->join('lists','lists.id','=','reminder_customers.list_id')
    						->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
    						->select('reminder_customers.*','lists.name','customers.wa_number','customers.created_at AS csrg')
    						->get();
    	return view('reminder.reminder-customer',['data'=>$remindercustomer]);
    }

/* end class reminder controller */    
}
