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
use App\Sender;

class EventController extends Controller
{

	PUBLIC FUNCTION JUSTCARBON() {
		//echo carbon::parse('2019-09-10 16:58:00')->subDays(5);
		echo carbon::parse('2019-09-10 16:58:00')->addDays(15);
	}

    public function index(){
    	$id = Auth::id();
    	$eventautoreply = Reminder::where([['reminders.user_id',$id],['reminders.days',0],['reminders.hour_time','=',null],['lists.is_event','=',1]])
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','reminders.*')
    			->get();

        $event = Reminder::where([['reminders.user_id',$id],['reminders.hour_time','<>',null],['lists.is_event','=',1]])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('lists.name','reminders.*')
                ->get();

    	return view('event.event',['data'=>$eventautoreply,'event'=>$event]);
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

        $checklist = Reminder::where([['list_id',$list_id],['hour_time','=',null]])->first();

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
        $sender = Sender::where('user_id',$user_id)->first();

        if($request->schedule == 0){
            $request->day = 0;
        }

        if($request->schedule > 0 &&  $request->day == 0){
           return redirect('eventform')->with('error_days','Please do not modify event days, if you want to use 0 days please use Hari H instead');
        }

        $rules = array(
            'list_id'=>['required'],
            'message'=>['required','max:3000'],
            'schedule'=>['required','numeric'],
            'day'=>['numeric'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('eventform')->with('error',$err);
        } else {
            //$req['id'] == checkbox list
                $reminder = new Reminder;
                $reminder->user_id = $user_id;
                $reminder->list_id = $request->list_id;
                $reminder->days = $request->day;
                $reminder->hour_time = $request->hour;
                $reminder->message = $request->message;
                $reminder->save();
        }

        /* if reminder stored / save successfully */
        if($reminder->save() == true){
            /* retrieve customer id */
                $event = Reminder::where([
                            ['reminders.id','=',$reminder->id],
                            ['reminders.status','=',1],
                            ['customers.status','=',1],
                            ['customers.list_id','=',$request->list_id],
                            ])->join('customers','customers.list_id','=','reminders.list_id')->select('reminders.*','customers.id AS csid')->get();
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event');
        }

        /* check whether user have customer */
        if(count($event) == 0){
            return redirect('eventform')->with('status','Your event has been set!');
        } else {
             foreach($event as $col){
                $remindercustomer = new ReminderCustomers;
                $remindercustomer->user_id = $user_id;
                $remindercustomer->list_id = $col->list_id;
                $remindercustomer->sender_id = $sender->id;
                $remindercustomer->reminder_id = $col->id;
                $remindercustomer->customer_id = $col->csid;
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


    public function addEventoldcode(Request $request){
        $user_id = Auth::id();
        $req = $request->all();
        $message = $req['message'];
        $event_date = $req['event_date'];
        $schedule = $req['schedule'];
        $sender = Sender::where('user_id',$user_id)->first();


        if((empty($req['day']) || empty($req['hour'])) && $schedule == 0)
        {
            $req['day'] = array(0);
            $req['hour'] = array($req['hour']);
            $days = $req['day'];
            $hour = $req['hour'];
        } else if((empty($req['day']) || empty($req['hour'])) && $schedule > 0) {
            $days = null;
            $hour = null;
        } else {
            $days = $req['day'];
            $hour = $req['hour'];
        }

        if($days == null || $hour == null) {
             return redirect('eventform')->with('errorday','Days and time should not blank');
        }

        $gettime = array_combine($days,$hour);

        $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
            'event_date'=>['required',new CheckDateEvent],
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
            /* retrieve customer id */
            foreach($req['id'] as $row=>$list_id){
                /* get customer data accoring on list */
                $event = Reminder::where([
                            ['reminders.event_date','=',$event_date],
                            ['reminders.list_id','=',$list_id],
                            ['reminders.status','=',1],
                            ['customers.status','=',1],
                            ['customers.list_id','=',$list_id],
                            ])->join('customers','customers.list_id','=','reminders.list_id')->select('reminders.*','customers.id AS csid')->get();

                foreach($event as $rows){
                    $eventcustomer[] = $rows;
                }

            }
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event');
        }

        /* check whether user have customer */
        if(count($eventcustomer) == 0){
            return redirect('eventform')->with('status','Your event has been set!');
        } else {
             foreach($eventcustomer as $col){
                $remindercustomer = new ReminderCustomers;
                $remindercustomer->user_id = $user_id;
                $remindercustomer->list_id = $col->list_id;
                $remindercustomer->sender_id = $sender->id;
                $remindercustomer->reminder_id = $col->id;
                $remindercustomer->customer_id = $col->csid;
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
    	$remindercustomer = ReminderCustomers::where([['reminder_customers.user_id','='                ,$id_user]
                            ])
    						->join('lists','lists.id','=','reminder_customers.list_id')
    						->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
                            ->rightJoin('reminders','reminders.id','=','reminder_customers.reminder_id')
    						->select('reminder_customers.*','lists.name','customers.wa_number','lists.event_date',
                                'reminders.days'
                            )->orderBy('reminder_customers.id','desc')
    						->get();
    	return view('event.event-customer',['data'=>$remindercustomer]);
    }

    public function displayEventSchedule(Request $request)
    {
        $id = $request->id;
        $event = Reminder::where([['id',$id],['days','>=',0],['hour_time','<>','0']])->first();

       $data = array(
            'date_event'=>$event->event_date,
            'day'=>$event->days,
            'hour'=>$event->hour_time,
       );

        return response()->json($data);
    }

    /* update event */

    public function updateEvent(Request $request)
    {
        $user_id = Auth::id();
        $req = $request->all();
        $message = $req['message'];
        $event_date = $req['event_date'];
        $schedule = $req['schedule'];
        $sender = Sender::where('user_id',$user_id)->first();


        if((empty($req['day']) || empty($req['hour'])) && $schedule == 0)
        {
            $req['day'] = array(0);
            $req['hour'] = array($req['hour']);
            $days = $req['day'];
            $hour = $req['hour'];
        } else if((empty($req['day']) || empty($req['hour'])) && $schedule > 0) {
            $days = null;
            $hour = null;
        } else {
            $days = $req['day'];
            $hour = $req['hour'];
        }

        if($days == null || $hour == null) {
            $error['days'] = 'Days and time should not blank';
            return response()->json($error);
        }

        $gettime = array_combine($days,$hour);

        $rules = array(
            'event_date'=>['required',new CheckDateEvent],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();
        $error['even_date'] = $err->first('event_date');

        /* Validator */
        if($validator->fails()){
            return response()->json($error);
        } else {
            /* prevent duplicate days */
            if($this->has_dupes($req['day']) == false){
                echo 'Do not use same value for day';
            } 
            //$req['id'] == checkbox list
            foreach($gettime as $day=>$hour){
                $update = array(
                    ''
                );
                $reminder = Reminder::where('id',$id_reminder)->update($update
                );
            }
        }

        /* if reminder stored / save successfully */
        if($reminder == true){
            $data['message'] = 'Your event has been updated';
        } else {
            $data['message'] = 'Error, unable to update event';
        }

        return response()->json($data);
    }

/* End event controller */
}
