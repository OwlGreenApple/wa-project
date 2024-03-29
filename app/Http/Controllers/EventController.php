<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
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
    			->select('lists.name','lists.label','reminders.*')
    			->get();

        $event = Reminder::where([['reminders.user_id',$id],['reminders.hour_time','<>',null],['lists.is_event','=',1]])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('lists.name','lists.event_date','lists.label','reminders.*')
                ->groupBy('lists.name')
                ->get();

    	return view('event.event',['data'=>$eventautoreply,'event'=>$event]);
    }

     public function displayEventList(Request $request)
    {
        $data = array();
        $id = Auth::id();
        $listid = $request->listid;
        $event = Reminder::where([['reminders.list_id','=',$listid],['reminders.user_id',$id],['reminders.hour_time','<>',null],['lists.is_event','=',1]])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('lists.name','lists.event_date','lists.label','reminders.*')
                ->get();

        return view('event.event-table',['event' => $event]);
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

        #Sender
        $listdata = UserList::where('id',$request->list_id)->select('wa_number')->first();
        $devicenumber = $listdata->wa_number;
        $sender = Sender::where([['user_id',$user_id],['wa_number','=',$devicenumber]])->first();

        if(is_null($sender))
        {
            return redirect('eventform')->with('errorsender','Sorry, this list phone number is not available');
        }

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
        if($event->count() == 0){
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
    	$remindercustomer = ReminderCustomers::where([['reminder_customers.user_id','='                ,$id_user],['lists.is_event','=',1]
                            ])
    						->join('lists','lists.id','=','reminder_customers.list_id')
    						->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
                            ->rightJoin('reminders','reminders.id','=','reminder_customers.reminder_id')
    						->select('reminder_customers.*','lists.name','customers.wa_number','lists.event_date',
                                'reminders.days','reminders.message'
                            )->orderBy('reminder_customers.id','desc')
    						->get();
    	return view('event.event-customer',['data'=>$remindercustomer]);
    }

    public function displayEventSchedule(Request $request)
    {
        $id = $request->id;
        $event = Reminder::where([['reminders.id',$id],['reminders.hour_time','<>',null],['lists.is_event','=',1]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.event_date','lists.id AS list_id')->first();

       $data = array(
            'date_event'=>$event->event_date,
            'day'=>$event->days,
            'hour'=>$event->hour_time,
            'list_id'=>$event->list_id,
       );

        return response()->json($data);
    }

    /* update event */

    public function updateEvent(Request $request)
    {
        $user_id = Auth::id();
        $sender = Sender::where('user_id',$user_id)->first();
        $id = $request->id;
        $list_id = $request->list_id;
        $data['error'] = false;
        $today = Carbon::now()->format('Y-m-d h:i');

         if($request->date_event < $today){
            $data = array(
                'error'=>true,
                'date_event'=>'Date event cannot be less than today',
            );
            return response()->json($data);
        } 

        $rules = array(
            'id'=>['required','numeric'],
            'list_id'=>['required','numeric'],
            'day'=>['required'],
            'hour'=>['required'],
            'date_event'=>['required'],
        );

        $validator = Validator::make($request->all(),$rules);
        $errors = $validator->errors();

        if($validator->fails()){
            $data = array(
                'id'=>$errors->first('id'),
                'list_id'=>$errors->first('list_id'),
                'day'=>$errors->first('day'),
                'hour'=>$errors->first('hour'),
                'date_event'=>$errors->first('date_event'),
                'error'=>true
            );
            return response()->json($data);
        }

        $event = Reminder::where('id',$id)->update([
            'days'=>$request->day,
            'hour_time'=>$request->hour,
        ]);

        if($event == true){
            $list = UserList::where('id',$list_id)->update(['event_date'=>$request->date_event]);
        } else {
            $data['message'] = 'Error, unable to update event date';
            return response()->json($data);
        }

        // if reminder and lists updated successfully
        if($list == true){
            $remindercustomer = ReminderCustomers::where('reminder_id',$id)->get();
        } else {
            $data['message'] = 'Error, unable to update event';
        } 

        if($remindercustomer->count() > 0){
           foreach($remindercustomer as $row){
                $wa_sender_id = $row->id_wa;
                if($wa_sender_id !== null){
                    $update_send_target = ReminderCustomers::where('id',$row->id)->update(['id_wa'=>null, 'status'=>0]);
                } else {
                    $update_send_target = null; //nothing to update
                }
            }
        } else {
            $update_send_target = null; //nothing to update
        }

        if($update_send_target == true || $update_send_target == null){
            $data['message'] = 'Your event reminder has been updated';
        } else {
            $data['message'] = 'Error, unable to update event';
        }

        return response()->json($data);

    }

    /* Change reminder and reminder-customer status */
    public function setEventStatus($id_reminder,$status){

        if(empty($id_reminder) && empty($status))
        {
             return redirect('event')->with('error','Error! Please do not change the values');
        }

        if($status == 0 || $status == 1){
           $pass = true;
        } else {
           return redirect('event')->with('error','Error! Please do not change the values');
        }
 
        /* From on to off */
        if($status == 1){
            $turn = 0;
            $turn_customer = 3;
        } else {
            $turn = 1;
            $turn_customer = 0;
        }

        $reminder = Reminder::where('id','=',$id_reminder)->update([
            'status'=>$turn
        ]);

        /* if correct then reminder's status updated */
        if($reminder == true){
            $remindercustomer =  ReminderCustomers::where([
                ['reminder_id','=',$id_reminder],
            ])->whereIn('status', [0,3])->update(['status'=> $turn_customer]);
        } else {
            return redirect('event')->with('error','Error-001! Unable to change reminder status');
        }

        /* if correct then reminder-customer's status updated */
        if($remindercustomer == true){
            return redirect('event')->with('message','Your reminder status has been change');
        } else {
            /* if there is no status = 0 */
            return redirect('event')->with('warning','Warning! Your reminder status just changed, but nothing reminder for customer');
        }
    }

    public function delEvent(Request $request){
        $id = $request->id;
        $id_user = Auth::id();
        $del_event = Reminder::where([['id','=',$id],['user_id','=',$id_user]])->delete();

        if($del_event == true){
            $event = ReminderCustomers::where([['reminder_id','=',$id],['user_id','=',$id_user]])->get();
        } else {
            $data['message'] = 'Sorry, cannot delete this event, there is error';
            return response()->json($data);
        }

        if($event->count() > 0){
            $event = ReminderCustomers::where([['reminder_id','=',$id],['user_id','=',$id_user]])->delete();
        } else {
            $data['message'] = 'Event has been deleted';
            return response()->json($data);
        }

        if($event == true){
            $data['message'] = 'Event has been deleted';
        } else {
            $data['message'] = 'Sorry, cannot delete this event, there is error';
        }
         return response()->json($data);
    }

    public function exportSubscriber(Request $request){
        $iduser = Auth::id();
        $id_list = $request->id;

        if(!empty($iduser) && !empty($id_list) || is_numeric($id_list))
        {
            $data['url'] = url("/export_csv/".$id_list."");
        } else {
            $data['url'] = 'You had logout, please login';
        }
        return response()->json($data);
    }

    public function exportEventSubscriber($id_list){
        $id_user = Auth::id();

        try{
            $id_list = decrypt($id_list);
        }catch(DecryptException $e){
            return redirect('event');
        }
       
        $customer = Customer::where([['list_id',$id_list],['user_id','=',$id_user]])->get();
       
        if(empty($id_list) || empty($id_user) || $customer->count() <= 0){
            return redirect('event');
        }
        return (new UsersExport($id_list))->download('users.csv');
    }

    function importCSVEvent(Request $request)
    {
        $id_list = $request->list_id_import;
        try{
            $id_list = decrypt($id_list);
        }catch(DecryptException $e){
            return redirect('event');
        }

        $file = $request->file('csv_file');
        Excel::import(new UsersImport($id_list), $file);
    }

/* End event controller */
}
