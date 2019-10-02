<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\UserList;
use App\Reminder;
use App\ReminderCustomers;
use App\Templates;
use App\Customer;
use Carbon\Carbon;
use App\Sender;

class ReminderController extends Controller
{

	/* Display created reminder */
    public function index(){
    	$id = Auth::id();
    	$list = Reminder::where([['reminders.user_id',$id],['lists.is_event','=',0],['reminders.days','>',0]])
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','lists.event_date','reminders.*')
    			->get();

        $listautoreply = Reminder::where([['reminders.user_id',$id],['lists.is_event','=',0],['reminders.days','=',0],['reminders.hour_time','=',null]])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('lists.name','lists.event_date','reminders.*')
                ->get();
    	return view('reminder.reminder',['data'=>$list,'autoreply'=>$listautoreply]);
    }

    /* Display form to create reminder auto reply */
     public function reminderAutoReply(){
        $id = Auth::id();
        $list = UserList::where([['user_id',$id],['is_event',0],['status',1]])->get();
        $templates = Templates::where('user_id',$id)->get();
        return view('reminder.reminder-autoreply',['data'=>$list, 'templates'=>$templates]);
    }

    public function addReminderAutoReply(Request $request){
        $user_id = Auth::id();
        $req = $request->all();
        $message = $req['message'];

        if(isset($req['listid'])){
            $list_id = $req['listid'];
        } else {
            return redirect('reminderautoreply')->with('error_autoreply','Please create message or event on list first');
        }

        $checklist = Reminder::where([['reminders.list_id',$list_id],['reminders.days',0],['reminders.hour_time',null],['lists.is_event','=',0]])
        ->join('lists','reminders.list_id','=','lists.id')
        ->select('reminders.*')
        ->first();

        if(!is_null($checklist))
        {
            return redirect('reminderautoreply')->with('error_autoreply','Sorry, you only allowed to create 1 auto reply per list');
        }

        $rules = array(
            'listid'=>['required'],
            'message'=>['required','max:3000'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('reminderautoreply')->with('error',$err);
        } else {
            $reminderautoreply = new Reminder;
            $reminderautoreply->user_id = $user_id;
            $reminderautoreply->list_id = $list_id;
            $reminderautoreply->message = $message;
            $reminderautoreply->save();
        }

        /* if reminder stored / save successfully */
        if($reminderautoreply->save() == true){
            return redirect('reminderautoreply')->with('status','Your reminder auto reply has been set!!');
        } else {
            return redirect('reminderautoreply')->with('status_error','Error!! failed to set reminder auto reply');
        }
    }

    /* Display form to create reminder schedule */
    public function reminderForm(){
    	$id = Auth::id();
    	$list = UserList::where([['user_id',$id],['is_event',0],['status',1]])->get();
        $templates = Templates::where('user_id',$id)->get();
    	return view('reminder.reminder-form',['data'=>$list, 'templates'=>$templates]);
    }

    /* Create and insert data reminder and reminder customer into database */
    public function addReminder(Request $request){
        $user_id = Auth::id();
        $message = $request->message;
        $days = $request->day;

        if(isset($request->list_id)){
            $list_id = $request->list_id;
        } else {
            return redirect('reminderform');
        }
        
        $sender = Sender::where('user_id',$user_id)->first();

        $rules = array(
            'list_id'=>['required'],
            'message'=>['required','max:3000'],
            'day'=>['required','numeric'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails()){
            return redirect('reminderform')->with('error',$err);
        } else {
            $reminder = new Reminder;
            $reminder->user_id = $user_id;
            $reminder->list_id = $list_id;
            $reminder->days = $days;
            $reminder->message = $message;
            $reminder->save();
            $created_date = $reminder->created_at;
        }

        # If data successfully inserted into reminder
        if($reminder->save() == true){
            # retrieve customer id 
            $customer = Customer::where([['list_id','=',$list_id],['status','=',1],])->get();
        } else {
            return redirect('reminderform')->with('status_error','Error!! failed to set reminder');
        }

        # Input eligible customer id
        $datacustomer = array();
        if($customer->count() > 0){
            foreach($customer as $rows){
                $customer_signup = Carbon::parse($rows->created_at);
                $adding_day = $customer_signup->addDays($days);
                if($adding_day >= $created_date){
                    $datacustomer[] = $rows;
                } 
            }
        } else {
            $datacustomer = null;
        }

        # indicate user doesn't have customer / subscriber
        if($datacustomer == null || count($datacustomer) == 0){
            return redirect('reminderform')->with('status','Your reminder has been set!');
        } else {
            # display data customer 
            foreach($datacustomer as $col){
                # retrieve reminder id according on created at 
                $reminder_get_id = Reminder::where([
                    ['list_id','=',$col->list_id],
                    ['created_at','=',$created_date],
                    ['status','=',1],
                ])->select('id')->get();

                $remindercustomer = new ReminderCustomers;
                foreach($reminder_get_id as $id_reminder){
                    $remindercustomer->user_id = $user_id;
                    $remindercustomer->list_id = $col->list_id;
                    $remindercustomer->sender_id = $sender->id;
                    $remindercustomer->reminder_id = $id_reminder->id;
                    $remindercustomer->customer_id = $col->id;
                    $remindercustomer->save();
                }

            } // end loop 
             # If successful insert data into reminder customer 
            if($remindercustomer->save() == true){
                return redirect('reminderform')->with('status','Your reminder has been set!!');
            } else {
                return redirect('reminderform')->with('status_error','Error!! failed to set reminder for customer');
            }
        }
       
    }

    /* Create and insert data reminder and reminder customer into database 
    public function addReminder(Request $request){
    	$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
    	$days = $req['day'];
        
        $sender = Sender::where('user_id',$user_id)->first();

        $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
            'day'=>['required','numeric'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails()){
            return redirect('reminderform')->with('error',$err);
        } else {
            foreach($req['id'] as $row=>$list_id){
                $reminder = new Reminder;
                $reminder->user_id = $user_id;
                $reminder->list_id = $list_id;
                $reminder->days = $days;
                $reminder->message = $message;
                $reminder->save();
            }
        }

    	/* If data successfully inserted into reminder 
    	if($reminder->save() == true){
    		foreach($req['id'] as $row=>$list_id){
    			/* retrieve customer id 
    			$customer = Customer::where([
    				['list_id','=',$list_id],
    				['status','=',1],
    			]);
    			
                /* Input eligible customer id
                $created_date = $reminder->created_at;
                foreach($customer->get() as $rows){
                    $customer_signup = Carbon::parse($rows->created_at);
                    $adding_day = $customer_signup->addDays($days);
                    if($adding_day >= $created_date){
                        $datacustomer[] = $rows;
                    }
                }
	    	}
    	} else {
    		return redirect('reminderform')->with('status_error','Error!! failed to set reminder');
    	}

        /* check whether user have customer 
        if(empty($datacustomer)){
            return redirect('reminderform')->with('status','Your reminder has been set!');
        } else {
            /* display data customer 
            foreach($datacustomer as $col){
                /* retrieve reminder id according on created at 
                $reminder_get_id = Reminder::where([
                    ['list_id','=',$col->list_id],
                    ['created_at','=',$created_date],
                    ['status','=',1],
                ])->select('id')->get();

                $remindercustomer = new ReminderCustomers;
                foreach($reminder_get_id as $id_reminder){
                    $remindercustomer->user_id = $user_id;
                    $remindercustomer->list_id = $col->list_id;
                    $remindercustomer->sender_id = $sender->id;
                    $remindercustomer->reminder_id = $id_reminder->id;
                    $remindercustomer->customer_id = $col->id;
                    $remindercustomer->save();
                }

            } /* end loop 
             /* If successful insert data into reminder customer 
            if($remindercustomer->save() == true){
                return redirect('reminderform')->with('status','Your reminder has been set!!');
            } else {
                return redirect('reminderform')->with('status_error','Error!! failed to set reminder for customer');
            }
        }
       
    }*/

    /* Display reminder customer */
    public function displayReminderCustomers()
    {
    	$id_user = Auth::id();
    	$remindercustomer = ReminderCustomers::where([['reminder_customers.user_id','='                ,$id_user],
                            ['lists.is_event','=',0],
                            ['reminders.hour_time','=',null],
                            ])
    						->join('lists','lists.id','=','reminder_customers.list_id')
    						->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
                            ->rightJoin('reminders','reminders.id','=','reminder_customers.reminder_id')
    						->select('reminder_customers.*','lists.name','customers.wa_number','customers.created_at AS csrg',
                                'reminders.message','reminders.days'
                            )->orderBy('reminder_customers.id','desc')
    						->get();
    	return view('reminder.reminder-customer',['data'=>$remindercustomer]);
    }

    /* Change reminder and reminder-customer status */
    public function setReminderStatus($id_reminder,$status){

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
            return redirect('reminder')->with('error','Error-001! Unable to changed reminder status');
        }

        /* if correct then reminder-customer's status updated */
        if($remindercustomer == true){
            return redirect('reminder')->with('message','Your reminder status just changed');
        } else {
            /* if there is no status = 0 */
            return redirect('reminder')->with('warning','Warning! Your reminder status just changed, but you do not have any message for subscribers');
        }
    }

    /* Update reminder message */
    public function updateReminderMessage(Request $request){
        $id = $request->id_reminder;
        $message = $request->message;

        $reminder = Reminder::where('id','=',$id)->update(['message'=>$message]);

        if($reminder == true){
            $data['msg'] = 'Reminder message just updated';
        } else {
            $data['msg'] = 'Error!! Unable to update reminder message';
        }

        return response()->json($data);
    } 

    /* Update reminder days */
    public function updateReminderDays(Request $request){
        $id = $request->id_reminder;
        $days = $request->days;

        if($days == 0 || empty($days) || preg_match('/^[a-z][A-Z]$/i',$days)){
            $data['msg'] = 'Invalid days';
            return response()->json($data);
        }

        $reminder = Reminder::where('id','=',$id)->update(['days'=>$days]);

        if($reminder == true){
            $data['msg'] = 'Reminder day just updated';
        } else {
            $data['msg'] = 'Error!! Unable to update reminder day';
        }

        return response()->json($data);
    }

    public function delReminder(Request $request){
        $id = $request->id;
        $id_user = Auth::id();
        $del_event = Reminder::where([['id','=',$id],['user_id','=',$id_user]])->delete();

        if($del_event == true){
            $event = ReminderCustomers::where([['reminder_id','=',$id],['user_id','=',$id_user]])->get();
        } else {
            $data['message'] = 'Sorry, cannot delete this reminder, there is error';
            return response()->json($data);
        }

        if($event->count() > 0){
            $event = ReminderCustomers::where([['reminder_id','=',$id],['user_id','=',$id_user]])->delete();
        } else {
            $data['message'] = 'Reminder has been deleted';
            return response()->json($data);
        }

        if($event == true){
            $data['message'] = 'Reminder has been deleted';
        } else {
            $data['message'] = 'Sorry, cannot delete this reminder, there is error';
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

    public function exportReminderSubscriber($id_list){
        $id_user = Auth::id();

        try{
            $id_list = decrypt($id_list);
        }catch(DecryptException $e){
            return redirect('reminder');
        }
       
        $customer = Customer::where([['list_id',$id_list],['user_id','=',$id_user]])->get();
       
        if(empty($id_list) || empty($id_user) || $customer->count() <= 0){
            return redirect('reminder');
        }
        return (new UsersExport($id_list))->download('users.csv');
    }

/* end class reminder controller */    
}
