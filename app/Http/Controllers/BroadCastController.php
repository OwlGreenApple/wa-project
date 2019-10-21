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
use Session;

class BroadCastController extends Controller
{

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

    /* Display broadcast customer page */
    public function displayBroadCastCustomer(){
    	$id_user = Auth::id();

        #broadcast reminder
    	$broadcastreminder = BroadCastCustomers::where([['broad_cast_customers.user_id','=',$id_user],['lists.is_event','=',0]])
    						->join('lists','lists.id','=','broad_cast_customers.list_id')
    						->rightJoin('customers','customers.id','=','broad_cast_customers.customer_id')
    						->select('lists.name','broad_cast_customers.*','customers.wa_number')
    						->get();

        #broadcast event
        $broadcastevent = BroadCastCustomers::where([['broad_cast_customers.user_id','=',$id_user],['lists.is_event','=',1]])
                            ->join('lists','lists.id','=','broad_cast_customers.list_id')
                            ->rightJoin('customers','customers.id','=','broad_cast_customers.customer_id')
                            ->select('lists.name','broad_cast_customers.*','customers.wa_number')
                            ->get();
    	return view('broadcast.broadcast-customer',['data'=>$broadcastreminder, 'event'=>$broadcastevent]);
    }

/* end class broadcast controller */    	
}
