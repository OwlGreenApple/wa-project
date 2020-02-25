<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Templates;
use App\Customer;
use Carbon\Carbon;
use App\User;
use App\Sender;
use App\ReminderCustomers;
use Session;
use DB;

class BroadCastController extends Controller
{

    /* Create broadcast list */
    public function saveBroadCast(Request $request){
        $user_id = Auth::id();
        $message = $request->message;
        $time_sending = $request->hour;
        $campaign = $request->campaign_name;
        $broadcast_schedule = $request->broadcast_schedule;
        $date_send = $request->date_send;

        if($broadcast_schedule == 0)
        {
            $list_id = $request->list_id;
            $group_name = null;
            $channel = null;
        }
        else if($broadcast_schedule == 1)
        {
            $list_id = 0;
            $group_name = $request->group_name;
            $channel = null;
        }
        else if($broadcast_schedule == 2)
        {
            $list_id = 0;
            $group_name = null;
            $channel = $request->channel_name;
        }
        else {
            return 'Please reload your browser and then try again without modify default value';
        }
        
        /*
        #prevent user to change value is_event
        try{
            $is_event = decrypt($request->is_event);
        }catch(DecryptException $e){
            return redirect('broadcast');
        }

        #determine redirect link
        if($is_event == 1){
            $link = 'broadcasteventform';
        } else {
            $link = 'broadcastform';
        }

        #prevent user to change value list id
        $checklist = UserList::where('is_event',$is_event)->whereIn('id',$req['id'])->select('is_event')->count();

        $total_list = count($req['id']);

        if($total_list !== $checklist){
            return redirect('broadcast');
        } 
        //print('<pre>'.print_r($checklist,true).'</pre>');
     
        /* Validator to limit max message character 
          $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
        );

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            $error = $validator->errors();
            return redirect($link)->with('error',$error);
        } else {
            
        }
        */
        $broadcast = new BroadCast;
        $broadcast->user_id = $user_id;
        $broadcast->list_id = $list_id;
        $broadcast->campaign = $campaign;
        $broadcast->group_name = $group_name;
        $broadcast->channel = $channel;
        $broadcast->day_send = $date_send;
        $broadcast->hour_time = $time_sending;
        $broadcast->message = $message;
        $broadcast->save();
        $broadcast_id = $broadcast->id;

        /* if successful inserted data broadcast into database then this run */
        if($broadcast->save()){         
            // retrieve customer id 
            $customer = Customer::where([
                ['user_id','=',$user_id],
                ['list_id','=',$list_id],
                ['status','=',1],
            ])->get();

        } else {
            return 'Error! Unable to create broadcast';
        }

        if($customer->count() > 0)
        {
            foreach($customer as $col){
                $broadcastcustomer = new BroadCastCustomers;
                $broadcastcustomer->broadcast_id = $broadcast_id;
                $broadcastcustomer->save();
            }
        } else if($broadcast_schedule == 0) {
            return 'Broadcast created, but will not send anything because you do not have subscriber';
        } else {
            return 'Your broadcast has been created';
        }

        if($broadcastcustomer->save()){
            return 'Your broadcast has been created';
        } else {
            return 'Error!!Your broadcast failed to create';
        }
    }

    /* Display broadcast */
    public function displayBroadCast(Request $request){
      $id_user = Auth::id();
      $data = array();
      $search = $request->search;

      if(empty($search))
      {
        $broadcasts = BroadCast::where([['broad_casts.user_id','=',$id_user]])->orderBy('id','desc')->get();
      }
      else
      {
        $broadcasts = BroadCast::where([['broad_casts.user_id','=',$id_user]])->orWhere('campaign','like','%'.$search.'%')->get();
      }

      if($broadcasts->count() > 0)
      {
          foreach($broadcasts as $row)
          {
              $lists = UserList::where([['id',$row->list_id],['user_id','=',$id_user]])->first();

              if(!is_null($lists))
              {
                  $label = $lists->label;
              }
              else 
              {
                  $label = null;
              }

              $reminder_customer = ReminderCustomers::where('reminder_id','=',$row->id_reminder)->select(DB::raw('COUNT("id") AS total_message'))->first();

              $reminder_customer_open = ReminderCustomers::where([['reminder_id','=',$row->id_reminder],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

              $data[] = array(
                  'id'=>$row->id,
                  'campaign' => $row->campaign,
                  'group_name' => $row->group_name,
                  'channel' => $row->channel,
                  'day_send' => Date('M d, Y',strtotime($row->day_send)),
                  'sending' => Date('h:i',strtotime($row->hour_time)),
                  'label' => $label,
                  'created_at' => Date('M d, Y',strtotime($row->created_at)),
                  'total_message' => $reminder_customer->total_message,
                  'sent_message' => $reminder_customer_open->total_sending_message,
              );
          }
      }

      return view('broadcast.broadcast',['broadcast'=>$data]);
    }

    public function delBroadcast(Request $request)
    {
        $user_id = Auth::id();
        $id = $request->id;

        try {
          BroadCast::where([['id',$id],['user_id',$user_id]])->delete();
          $success = true;
        }
        catch(Exception $e)
        {
           return response()->json(['message'=>'Sorry, unable to delete broadcast, contact administrator']);
        }

        if($success == true)
        {
          $broadcastcustomer = BroadCastCustomers::where('broadcast_id','=',$id)->get();
        }

        if($broadcastcustomer->count() > 0)
        {
             BroadCastCustomers::where('broadcast_id','=',$id)->delete();
        }
        return response()->json(['message'=>'Your broadcast has been deleted successfully']);
    }

    public function cehckBroadcastType(Request $request)
    {
        $user_id = Auth::id();
        $id = $request->id;

        $broadcast = BroadCast::where([['id',$id],['user_id',$user_id]])->first();

        $data = array(
          'list_id' => $broadcast->list_id,
          'group_name' => $broadcast->group_name,
          'channel' => $broadcast->channel,
          'campaign' => $broadcast->campaign,
          'day_send' => $broadcast->day_send,
          'hour_time' => $broadcast->hour_time,
          'message' => $broadcast->message,
        );

        return response()->json($data);
    }

    public function duplicateBroadcast(Request $request)
    {
        $user_id = Auth::id();
        $list_id = $request->list_id;
        $broadcast_id = $request->id;
        $broadcast_name = $request->campaign_name;
        $broadcast_date =  $request->date_send;
        $broadcast_sending =  $request->hour;
        $broadcast_message =  $request->message;
        $broadcast_group_name =  $request->group_name;
        $broadcast_channel =  $request->channel_name;
        $broadcast = new BroadCast;

        if(empty($list_id))
        {
            $list_id = 0;
        }

        if($list_id > 0)
        {
          $broadcast->user_id = $user_id;
          $broadcast->list_id = $list_id;
          $broadcast->campaign = $broadcast_name;
          $broadcast->day_send = $broadcast_date;
          $broadcast->hour_time = $broadcast_sending;
          $broadcast->message = $broadcast_message;
          $broadcast->save();
        }
        else if(empty($list_id) && !empty($broadcast_group_name))
        {
          $broadcast->user_id = $user_id;
          $broadcast->list_id = $list_id;
          $broadcast->campaign = $broadcast_name;
          $broadcast->group_name = $broadcast_group_name;
          $broadcast->day_send = $broadcast_date;
          $broadcast->hour_time = $broadcast_sending;
          $broadcast->message = $broadcast_message;
          $broadcast->save();
        }
        else if(empty($list_id) && !empty($broadcast_channel))
        {
          $broadcast->user_id = $user_id;
          $broadcast->list_id = $list_id;
          $broadcast->campaign = $broadcast_name;
          $broadcast->channel = $broadcast_channel;
          $broadcast->day_send = $broadcast_date;
          $broadcast->hour_time = $broadcast_sending;
          $broadcast->message = $broadcast_message;
          $broadcast->save();
        }

        if($broadcast->save() && $list_id > 0)
        { 
          $broadcastcustomer = ReminderCustomers::where([['user_id',$user_id],['reminder_id',$event_id]])->get();
        }
        else {
           return response()->json(['message'=>'Sorry, cannot duplicate your campaign, please call administrator']);
        }

        if($remindercustomer->count() > 0)
        {
          foreach($remindercustomer as $row)
          {
              $eventcustomer = new ReminderCustomers;
              $eventcustomer->user_id = $user_id;
              $eventcustomer->list_id = $list_id;
              $eventcustomer->reminder_id = $newreminderid;
              $eventcustomer->customer_id = $row->customer_id;
              $eventcustomer->save();
          }
        }
        else 
        {
            return response()->json(['message'=>'Your campaign duplicated successfully']);
        }

        if($eventcustomer->save())
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

    public function index(){
    	$id = Auth::id();

        #broadcast reminder
    	$broadcast_reminder = BroadCast::where([['broad_casts.user_id',$id],['lists.is_event','=',0]])
    			->join('lists','broad_casts.list_id','=','lists.id')
    			->select('lists.name','broad_casts.*')
    			->get();

        #broadcast event
        $broadcast_event = BroadCast::where([['broad_casts.user_id',$id],['lists.is_event','=',1]])
                ->join('lists','broad_casts.list_id','=','lists.id')
                ->select('lists.name','broad_casts.*')
                ->get();
    	return view('broadcast.broadcast',['data'=>$broadcast_reminder,'event'=>$broadcast_event]);
    }

    /* Broadcast form reminder */
    public function FormBroadCast(){
    	$id_user = Auth::id();
    	$userlist = UserList::where([['user_id','=',$id_user],['is_event','=',0]])->get();
    	$templates = Templates::where('user_id','=',$id_user)->get();
    	return view('broadcast.broadcast-form',['data'=>$userlist,'templates'=>$templates]);
    }
 
     /* Broadcast form event */
    public function eventFormBroadCast(){
        $id_user = Auth::id();
        $userlist = UserList::where([['user_id','=',$id_user],['is_event','=',1]])->get();
        $templates = Templates::where('user_id','=',$id_user)->get();
        return view('broadcast.broadcast-event-form',['data'=>$userlist,'templates'=>$templates]);
    }

    /* Create broadcast list */
    public function createBroadCast(Request $request){
    	$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
        
        #prevent user to change value is_event
        try{
            $is_event = decrypt($request->is_event);
        }catch(DecryptException $e){
            return redirect('broadcast');
        }

        #determine redirect link
        if($is_event == 1){
            $link = 'broadcasteventform';
        } else {
            $link = 'broadcastform';
        }

        #prevent user to change value list id
        $checklist = UserList::where('is_event',$is_event)->whereIn('id',$req['id'])->select('is_event')->count();

        $total_list = count($req['id']);

        if($total_list !== $checklist){
            return redirect('broadcast');
        } 
        //print('<pre>'.print_r($checklist,true).'</pre>');
     
    	/* Validator to limit max message character */
    	  $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
        );

        $validator = Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		$error = $validator->errors();
    		return redirect($link)->with('error',$error);
    	} else {
    		foreach($req['id'] as $row=>$list_id){
	    		$broadcast = new BroadCast;
	    		$broadcast->user_id = $user_id;
	    		$broadcast->list_id = $list_id;
	    		$broadcast->message = $message;
	    		$broadcast->save();
                $created_date = $broadcast->created_at;
                $broadcast_id = $broadcast->id;
	    	}
    	}

    	/* if successful inserted data broadcast into database then this run */
    	if($broadcast->save() == true){
            if(count($req['id']) > 1){
                # retrieve customer id 
                $customer = Customer::where([
                    ['customers.user_id','=',$user_id],
                    ['customers.status','=',1],
                    ['broad_casts.created_at','=',$created_date],
                ])->leftJoin('broad_casts','broad_casts.list_id','=','customers.list_id')
                  ->rightJoin('lists','lists.id','=','customers.list_id')
                  ->whereIn('customers.list_id', $req['id'])
                  ->select('customers.id','broad_casts.id AS bid','lists.id AS lid')
                  ->groupBy('customers.wa_number')
                  ->get();
            } else {
                # retrieve customer id 
                $customer = Customer::where([
                    ['customers.user_id','=',$user_id],
                    ['customers.status','=',1],
                    ['customers.list_id','=',$req['id'][0]],
                    ['broad_casts.id','=',$broadcast_id],
                ])->join('broad_casts','broad_casts.list_id','=','customers.list_id')
                  ->join('lists','lists.id','=','customers.list_id')
                  ->select('customers.id','broad_casts.id AS bid','lists.id AS lid')
                  ->get();
            }

    	} else {
    		return redirect($link)->with('status_error','Error! Unable to create broadcast');
    	}

        if($customer->count() > 0)
        {
            foreach($customer as $col){
                $listdata = UserList::where('id',$col->lid)->select('wa_number')->first();
                $devicenumber = $listdata->wa_number;
                $sender = Sender::where([['user_id',$user_id],['wa_number','=',$devicenumber]])->first();

                $broadcastcustomer = new BroadCastCustomers;
                $broadcastcustomer->user_id = $user_id;
                $broadcastcustomer->list_id = $col->lid;
                $broadcastcustomer->sender_id = $sender->id;
                $broadcastcustomer->broadcast_id = $col->bid;
                $broadcastcustomer->customer_id = $col->id;
                $broadcastcustomer->message = $message;
                $broadcastcustomer->save();
            }

            if($broadcastcustomer->save() == true){
                $success = true;
            } else {
                $success = false;
            }
        } else {
            $success = null;
        }

    	/* if successful inserted data broadcast-customer into database then this function run */
    	if($success == true){
          return redirect($link)->with('status','Your message has been created');
    	} else if($success == null) {
    		 return redirect($link)->with('status_warning','Broadcast created, but nothing to send because you have no subscribers');
    	} else {
            return redirect($link)->with('status_error','Error!!Your message failed to create');
        }
    }

/* end class broadcast controller */    	
}
