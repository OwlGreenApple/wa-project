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
    	$list = Reminder::where([['reminders.user_id',$id],['reminders.is_event',1]])
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','reminders.*')
    			->get();
    	return view('event.event',['data'=>$list]);
    }

     public function eventForm(){
     	$id = Auth::id();
    	$list = UserList::where([['user_id',$id],['status',1]])->get();
        $templates = Templates::where('user_id',$id)->get();
    	return view('event.event-form',['data'=>$list, 'templates'=>$templates]);
    }

    public function addEvent(Request $request){
		$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
    	$event_date = $req['event_date'];

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

        	$days = $req['day'];
        	//$req['id'] == checkbox list
            foreach($days as $day){
	    	 	foreach($req['id'] as $row=>$list_id){
		 		    $reminder = new Reminder;
	                $reminder->user_id = $user_id;
	                $reminder->list_id = $list_id;
	                $reminder->is_event = 1;
	                $reminder->event_date = $event_date;
	                $reminder->days = $day;
	                $reminder->message = $message;
	                $reminder->save();
	    	 	}
	        }
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
        	
    		 $created_date = $reminder->created_at;
    		 foreach($datacustomer as $col){
                /* get reminder id */
                $reminder_get_id = Reminder::where([
                	['list_id','=',$col->list_id],
                	['created_at','=',$created_date],
                    ['event_date','=',$event_date],
                    ['is_event','=',1],
                    ['status','=',1],
                ])->select('id')->get();

                foreach($reminder_get_id as $id_reminder){
	                //echo $col->list_id.'=='.$id_reminder->id.'<br>';
	                $remindercustomer = new ReminderCustomers;
                    $remindercustomer->user_id = $user_id;
                    $remindercustomer->list_id = $col->list_id;
                    $remindercustomer->reminder_id = $id_reminder->id;
                    $remindercustomer->customer_id = $col->id;
                    $remindercustomer->message = $message;
                    $remindercustomer->save();
	            }
        	}

             /* If successful insert data into event customer */
            if($remindercustomer->save() == true){
                return redirect('eventform')->with('status','Your event has been set!!');
            } else {
                return redirect('eventform')->with('status_error','Error!! failed to set event for customer');
            }
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

/* End event controller */
}
