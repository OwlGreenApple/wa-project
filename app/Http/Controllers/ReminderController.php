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
        $templates = Templates::where('user_id',$id)->get();
    	return view('reminder.reminder-form',['data'=>$list, 'templates'=>$templates]);
    }

    /* Create and insert data reminder and reminder customer into database */
    public function addReminder(Request $request){
    	$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
    	$days = $req['day'];

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

    	/* If data successfully inserted into reminder */
    	if($reminder->save() == true){
    		foreach($req['id'] as $row=>$list_id){
    			/* retrieve customer id */
    			$customer = Customer::where([
    				['list_id','=',$list_id],
    				['status','=',1],
    			]);
    			
                /* Input eligible customer id */
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

        /* check whether user have customer */
        if(empty($datacustomer)){
            return redirect('reminderform')->with('status','Your reminder has been set!');
        } else {
            /* display data customer */
            foreach($datacustomer as $col){
                /* retrieve reminder id according on created at */
                $reminder_get_id = Reminder::where([
                    ['list_id','=',$col->list_id],
                    ['created_at','=',$created_date],
                ])->select('id')->get();

                $remindercustomer = new ReminderCustomers;
                foreach($reminder_get_id as $id_reminder){
                    $remindercustomer->user_id = $user_id;
                    $remindercustomer->list_id = $col->list_id;
                    $remindercustomer->reminder_id = $id_reminder->id;
                    $remindercustomer->customer_id = $col->id;
                    $remindercustomer->message = $message;
                    $remindercustomer->save();
                }

            } /* end loop */
             /* If successful insert data into reminder customer */
            if($remindercustomer->save() == true){
                return redirect('reminderform')->with('status','Your reminder has been set!!');
            } else {
                return redirect('reminderform')->with('status_error','Error!! failed to set reminder for customer');
            }
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
            return redirect('reminder')->with('error','Error-001! Unable to change reminder status');
        }

        /* if correct then reminder-customer's status updated */
        if($remindercustomer == true){
            return redirect('reminder')->with('message','Reminder status has been changed');
        } else {
            /* if there is no status = 0 */
            return redirect('reminder')->with('error','Warning!! Unable to change reminder status due there is no new data');
        }
    }

    /* Update reminder message */
    public function updateReminderMessage(Request $request){
        $id = $request->id_reminder;
        $message = $request->message;

        $reminder = Reminder::where('id','=',$id)->update(['message'=>$message]);

        if($reminder == true){
            $data['msg'] = 'Reminder message has been updated';
        } else {
            $data['msg'] = 'Error!! Unable to update reminder message';
        }

        return response()->json($data);
    }

/* end class reminder controller */    
}
