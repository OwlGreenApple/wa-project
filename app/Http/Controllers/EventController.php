<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckDateEvent;
use App\UserList;
use App\Reminder;
use App\ReminderCustomers;
use App\Templates;
use App\Customer;
use Carbon\Carbon;

class EventController extends Controller
{

	PUBLIC FUNCTION JUSTCARBON() {
		//echo carbon::parse('2019-09-10 16:58:00')->subDays(5);
		echo carbon::parse('2019-09-10 16:58:00')->addDays(15);
	}

    public function index(){
    	$id = Auth::id();
    	$list = Reminder::where([['reminders.user_id',$id]])
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','reminders.*')
    			->get();
    	return view('event.event',['data'=>$list]);
    }

    public function eventAutoReply(){
        $id = Auth::id();
        $list = UserList::where([['user_id',$id],['is_event',1],['status',1]])->get();
        $templates = Templates::where('user_id',$id)->get();
        return view('event.event-autoreply',['data'=>$list, 'templates'=>$templates]);
    }

    /* perform autoreply */
    public function addEventAutoReply(Request $request){
		$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
        $list_id = $req['listid'];

        $checklist = Reminder::where([['list_id',$list_id],['days','=',0]])->first();

        if(!is_null($checklist))
        {
            return redirect('eventautoreply')->with('error_autoreply','Sorry, you only allowed to create 1 auto reply per list');
        }

        $rules = array(
            'listid'=>['required'],
            'message'=>['required','max:3000'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('eventautoreply')->with('error',$err);
        } else {
 		    $eventautoreply = new Reminder;
            $eventautoreply->user_id = $user_id;
            $eventautoreply->list_id = $list_id;
            $eventautoreply->message = $message;
            $eventautoreply->save();
        }

        /* if reminder stored / save successfully */
        if($eventautoreply->save() == true){
        	return redirect('eventautoreply')->with('status','Your event has been set!!');
        } else {
        	return redirect('eventautoreply')->with('status_error','Error!! failed to set event auto reply');
        }
    }


    /* Create scheduled message */
    public function eventForm(){
        $id = Auth::id();
        $list = UserList::where([['user_id',$id],['is_event',1],['status',1]])->get();
        $templates = Templates::where('user_id',$id)->get();
        return view('event.event-form',['data'=>$list, 'templates'=>$templates]);
    }

    public function addEvent(Request $request){
        $user_id = Auth::id();
        $req = $request->all();
        $message = $req['message'];
        $event_date = $req['event_date'];
        $days = $req['day'];
        $hour = $req['hour'];
        $gettime = array_combine($days,$hour);

        $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
            'event_date'=>['required',new CheckDateEvent],
            'day'=>['required'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('eventform')->with('error',$err);
        } else {
            /* prevent duplicate days */
            if($this->has_dupes($req['day']) == false){
                return redirect('eventform')->with('error_day','Do not use same value for day');
            } 
            //$req['id'] == checkbox list

            foreach($gettime as $day=>$hour){
                foreach($req['id'] as $row=>$list_id){
                    $reminder = new Reminder;
                    $reminder->user_id = $user_id;
                    $reminder->list_id = $list_id;
                    $reminder->event_date = $event_date;
                    $reminder->days = $day;
                    $reminder->hour_time = $hour;
                    $reminder->message = $message;
                    $reminder->save();
                }
            }
            $created_date = $reminder->created_at;
        }

        /* if reminder stored / save successfully */
        if($reminder->save() == true){
            foreach($req['id'] as $row=>$list_id){
                /* retrieve customer id */
                foreach($req['id'] as $row=>$list_id){
                    /* get customer data accoring on list */
                    $customer = Customer::where([
                        ['list_id','=',$list_id],
                        ['status','=',1],
                    ]);
                    
                    /* Input eligible customer id */
                    foreach($customer->get() as $rows){
                        $datacustomer[] = $rows;
                    }
                }
            }
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event');
        }

        /* check whether user have customer */
        if(empty($datacustomer)){
            return redirect('eventform')->with('status','Your event has been set!');
        } else {
             foreach($datacustomer as $col){
                /* get reminder id */
                $reminder_get_id = Reminder::where([
                    ['list_id','=',$col->list_id],
                    ['created_at','=',$created_date],
                    ['event_date','=',$event_date],
                    ['status','=',1],
                ])->select('id')->get();

                foreach($reminder_get_id as $id_reminder){
                    $remindercustomer = new ReminderCustomers;
                    $remindercustomer->user_id = $user_id;
                    $remindercustomer->list_id = $col->list_id;
                    $remindercustomer->reminder_id = $id_reminder->id;
                    $remindercustomer->customer_id = $col->id;
                    $remindercustomer->message = $message;
                    $remindercustomer->save();
                }
             }
        }

        /* If successful insert data into event customer */
        if($remindercustomer->save() == true){
            return redirect('eventform')->with('status','Your event has been set!!');
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event for customer');
        }

    }

    /* To check duplicate array */
    function has_dupes($array) {
    	foreach (array_count_values($array) as $value) {
    		 $value;
    	}
    	if($value > 1){
    		return false;
    	} else {
    		return true;
    	}
	}

    /* Disable or enable autoreply */
    public function turnEventAutoReply($id,$status)
    {

        if(empty($id))
        {
            return redirect('event');
        } 

        $eventautoreply = Reminder::where('id',$id)->update(['status'=>$status]);
        if($eventautoreply == true)
        {
            return redirect('event')->with('message','Event auto reply status has successully changed');
        } else {
            return redirect('event')->with('warning','Error! Unable to change event autoreply status');
        }
    }

	/* Display reminder customer */
    public function displayEventCustomers()
    {
    	$id_user = Auth::id();
    	$remindercustomer = ReminderCustomers::where([['reminder_customers.user_id','='                ,$id_user],
                            ['reminders.is_event','=',1]])
    						->join('lists','lists.id','=','reminder_customers.list_id')
    						->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
                            ->rightJoin('reminders','reminders.id','=','reminder_customers.reminder_id')
    						->select('reminder_customers.*','lists.name','customers.wa_number','reminders.event_date',
                                'reminders.is_event','reminders.days'
                            )->orderBy('reminder_customers.id','desc')
    						->get();
    	return view('event.event-customer',['data'=>$remindercustomer]);
    }

/* End event controller */
}
