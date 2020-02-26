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
use App\Campaign;
use DB;

class ReminderController extends Controller
{
    /* Create and insert data reminder and reminder customer into database */
    public function saveAutoReponder(Request $request){
        $user_id = Auth::id();
        $list_id = $request->list_id;
        $message = $request->message;
        $days = $request->day;
        $package = $request->campaign_name;
        //$mailsubject = $request->mailsubject;
        //$mailmessage = $request->mailmessage;

        /*
        if(isset($request->list_id)){
            $list_id = $request->list_id;
        } else {
            return redirect('reminderform');
        }

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
           
        }
        */

        $campaign_type = 1;


        $campaign = new Campaign;
        $campaign->name =  $request->campaign_name;
        $campaign->type =  $campaign_type;
        $campaign->list_id = $request->list_id;
        $campaign->user_id = $user_id;
        $campaign->save();
        $campaign_id = $campaign->id;
    
        if($campaign->save())
        {
          $reminder = new Reminder;
          $reminder->user_id = $user_id;
          $reminder->list_id = $list_id;
          $reminder->campaign_id = $campaign_id;
          $reminder->days = $days;
          $reminder->hour_time = $request->hour;
          $reminder->message = $message;
          $reminder->save();
          $created_date = $reminder->created_at;
        }
        else
        {
          return 'Sorry, cannot create event, please contact administrator';
        }

        // If data successfully inserted into reminder
        if($reminder->save() == true){
            // retrieve customer id 
            $customer = Customer::where([['user_id','=',$user_id],['list_id','=',$list_id],['status','=',1],])->get();
        } else {
            return 'Error!! failed to set reminder';
        }

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
            return 'Your reminder has been set!';
        }

        // display data customer 
        foreach($datacustomer as $col){
            // retrieve reminder id according on created at 
            $reminder_get_id = Reminder::where([
                ['list_id','=',$col->list_id],
                ['is_event','=',0],
                ['created_at','=',$created_date],
                ['status','=',1],
            ])->select('id')->get();

            $remindercustomer = new ReminderCustomers;
            foreach($reminder_get_id as $id_reminder){
                $remindercustomer->user_id = $user_id;
                $remindercustomer->list_id = $col->list_id;
                $remindercustomer->reminder_id = $id_reminder->id;
                $remindercustomer->customer_id = $col->id;
                $remindercustomer->save();
            }

        } // end loop

        // If successful insert data into reminder customer 
        if($remindercustomer->save()){
            return 'Your reminder has been set!!';
        } else {
            return 'Error!! failed to set reminder for customer';
        }
    }

    public function displayReminderList(Request $request)
    {
        $data = array();
        $id = Auth::id();
        $type = $request->type;

        if($type <> 1)
        {
            return 'Please do not modify default value';
        }

        $reminder = Campaign::where([['campaigns.user_id',$id],['campaigns.type',$type],['reminders.is_event','=',0]])
            ->join('reminders','reminders.campaign_id','=','campaigns.id')
            ->join('lists','lists.id','=','campaigns.list_id')
            ->select('campaigns.name','lists.label','campaigns.created_at','reminders.*','reminders.id AS id_reminder')
            ->orderBy('campaigns.id','desc')
            ->get();

        if($reminder->count() > 0)
        {
           foreach($reminder as $row)
           {
              $sending = $row->days;
              if($sending > 1)
              {
                  $message = 'days from subscriber join on your list';
              }
              else{
                  $message = 'day from subscriber join on your list';
              }

              $reminder_customer = ReminderCustomers::where([['reminder_id','=',$row->id_reminder]])->select(DB::raw('COUNT("id") AS total_message'))->first();

              $reminder_customer_open = ReminderCustomers::where([['reminder_id','=',$row->id_reminder],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

              $data[] = array(
                  'id'=>$row->id,
                  'campaign_id'=>$row->campaign_id,
                  'campaign_name' => $row->name,
                  'sending' => $sending.' '.$message,
                  'label' => $row->label,
                  'created_at' => Date('M d, Y',strtotime($row->created_at)),
                  'total_message' => $reminder_customer->total_message,
                  'sent_message' => $reminder_customer_open->total_sending_message,
              );
           }
        }
        return view('reminder.reminder-table',['reminder' => $data]);
    }

    public function delReminder(Request $request)
    {
        $id = $request->id;
        $user_id = Auth::id();
        $reminder = Reminder::where([['user_id','=',$user_id],['id',$id]])->first();
        $campaign_id = $reminder->campaign_id;

        try {
          Reminder::where([['user_id','=',$user_id],['id',$id]])->delete();
          Campaign::where([['id',$campaign_id],['user_id',$user_id]])->delete();
          $success = true;
        }
        catch(Exception $e)
        {
          return response()->json(['message'=>'Sorry, unable to delete auto responder, contact administrator']);
        }

        if($success == true)
        {
          $remindercustomer = ReminderCustomers::where('reminder_id','=',$id)->get();
        }

        if($remindercustomer->count() > 0)
        {
             ReminderCustomers::where('reminder_id','=',$id)->delete();
        }
        return response()->json(['message'=>'Your auto responder has been deleted successfully']);
    }

    public function duplicateReminder(Request $request)
    {
        $user_id = Auth::id();
        $reminder_id = $request->id;
        $campaign_name = $request->campaign_name;

        $row_reminder = Reminder::where([['id',$reminder_id],['user_id',$user_id],['is_event',0]])->first();

        if(!is_null($row_reminder))
        {
          $list_id = $row_reminder->list_id;
          $reminder_day = $row_reminder->days;
          $reminder_sending = $row_reminder->hour_time;
          $reminder_message = $row_reminder->message;

          $campaign = new Campaign;
          $campaign->name = $campaign_name;
          $campaign->type = 1;
          $campaign->list_id = $list_id;
          $campaign->user_id = $user_id;
          $campaign->save();
          $campaign_id = $campaign->id;

          $reminder = new Reminder;
          $reminder->user_id = $user_id;
          $reminder->list_id = $list_id;
          $reminder->campaign_id = $campaign_id;
          $reminder->is_event = 0;
          $reminder->days = $reminder_day;
          $reminder->hour_time = $reminder_sending;
          $reminder->message = $reminder_message;
          $reminder->save();
          $newreminderid = $reminder->id;
        }
        else 
        {
           return response()->json(['message'=>'Id is not registered, please reload or refresh your browser!']);
        }

        if($reminder->save())
        { 
          $remindercustomer = ReminderCustomers::where([['user_id',$user_id],['reminder_id',$reminder_id]])->get();
        }
        else {
           return response()->json(['message'=>'Sorry, cannot duplicate your campaign, please call administrator']);
        }

        if($remindercustomer->count() > 0)
        {
          foreach($remindercustomer as $row)
          {
              $respondercustomer = new ReminderCustomers;
              $respondercustomer->user_id = $user_id;
              $respondercustomer->list_id = $list_id;
              $respondercustomer->reminder_id = $newreminderid;
              $respondercustomer->customer_id = $row->customer_id;
              $respondercustomer->save();
          }
        }
        else 
        {
            return response()->json(['message'=>'Your campaign duplicated successfully']);
        }

        if($respondercustomer->save())
        {
            return response()->json(['message'=>'Your campaign duplicated successfully']);
        }
        else
        {
            return response()->json(['message'=>'Sorry, cannot duplicate your campaign, please call administrator']);
        }
    }

    /****************************************************************************************
                                            OLD CODES
    ****************************************************************************************/

	/* Display created reminder */
    public function index(){
    	$id = Auth::id();
    	$list = Reminder::where([['reminders.user_id',$id],['lists.is_event','=',0],['reminders.days','>',0]])
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','lists.label','reminders.*')
                ->groupBy('lists.name')
    			->get();

        $listautoreply = Reminder::where([['reminders.user_id',$id],['lists.is_event','=',0],['reminders.days','=',0],['reminders.hour_time','=',null]])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('lists.name','lists.event_date','lists.label','reminders.*')
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
        $package = $request->package;
        $mailsubject = $request->mailsubject;
        $mailmessage = $request->mailmessage;

        if(isset($request->list_id)){
            $list_id = $request->list_id;
        } else {
            return redirect('reminderform');
        }

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
            $reminder->package = $package;
            $reminder->message = $message;
            $reminder->subject = $mailsubject;
            $reminder->mail = $mailmessage;
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

    /* Display reminder customer */
    public function displayReminderCustomers()
    {
    	$id_user = Auth::id();
    	$remindercustomer = ReminderCustomers::where([['reminder_customers.user_id','=',$id_user],
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
        $data = [
            'message'=>$message,
            'subject'=>$request->subject,
            'mail'=>$request->mailtext,
        ];

        $reminder = Reminder::where('id','=',$id)->update($data);

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

    /*
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
    */

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
