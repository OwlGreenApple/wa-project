<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
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
    	$list = BroadCast::where('broad_casts.user_id',$id)
    			->join('lists','broad_casts.list_id','=','lists.id')
    			->select('lists.name','broad_casts.*')
    			->get();
    	return view('broadcast.broadcast',['data'=>$list]);
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
    	$msg = array('message'=>$message);
        $sender = Sender::where('user_id',$user_id)->first();
        
        /*
        $checkarray = array();
        foreach($req['id'] as $row=>$list_id){
             $customer = Customer::where([
                ['list_id','=',$list_id],
                ['status','=',1],
            ])->get();
             
            if(count($req['id']) > 1){
                foreach($customer as $rows){
                    //$checkarray[] = $rows;
                    echo $rows->wa_number;
                }
            }
        }

         print('<pre>'.print_r(array_unique($checkarray),true).'</pre>');
            die('');

         */   

    	/* Validator to limit max message character */
    	  $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
        );

        $validator = Validator::make($request->all(),$rules);

    	if($validator->fails()){
    		$error = $validator->errors();
    		return redirect('broadcastform')->with('error',$error);
    	} else {
    		foreach($req['id'] as $row=>$list_id){
	    		$broadcast = new BroadCast;
	    		$broadcast->user_id = $user_id;
	    		$broadcast->list_id = $list_id;
	    		$broadcast->message = $message;
	    		$broadcast->save();
	    	}
    	}

    	/* if successful inserted data broadcast into database then this run */
    	if($broadcast->save() == true){
    		
    		foreach($req['id'] as $row=>$list_id){
    			/* retrieve customer id */
                 $customer = Customer::where([
                    ['list_id','=',$list_id],
                    ['status','=',1],
                ])->distinct('wa_number')->get();
    			/* retrieve broadcast id according on created at */
    			$created_date = $broadcast->created_at;
    			$broadcast_get_id = BroadCast::where([
    				['list_id','=',$list_id],
    				['created_at','=',$created_date],
    			])->select('id')->get();
    			/* insert into broadcast customer */
    			foreach($customer as $col){
                    $check_wa_number = Customer::where('wa_number','=',$col->wa_number)->first();
    				foreach($broadcast_get_id as $id_broadcast){
    					$broadcastcustomer = new BroadCastCustomers;
			    		$broadcastcustomer->user_id = $user_id;
			    		$broadcastcustomer->list_id = $list_id;
                        $broadcastcustomer->sender_id = $sender->id;
			    		$broadcastcustomer->broadcast_id = $id_broadcast->id;
			    		$broadcastcustomer->customer_id = $col->id;
			    		$broadcastcustomer->message = $message;
			    		$broadcastcustomer->save();
    				}
    			}
	    	}
    	} else {
    		return redirect('broadcastform')->with('status_error','Error! Unable to create broadcast');
    	}

    	/* if successful inserted data broadcast-customer into database then this function run */
    	if($broadcastcustomer->save() == true){
    		return redirect('broadcastform')->with('status','Your message has been created');
    	} else {
    		return redirect('broadcastform')->with('status_error','Error!!Your message failed to create');
    	}
    }

    /* Display broadcast customer page */
    public function displayBroadCastCustomer(){
    	$id_user = Auth::id();
    	$broadcastcustomer = BroadCastCustomers::where('broad_cast_customers.user_id','=',$id_user)
    						->join('lists','lists.id','=','broad_cast_customers.list_id')
    						->rightJoin('customers','customers.id','=','broad_cast_customers.customer_id')
    						->select('lists.name','broad_cast_customers.*','customers.wa_number')
    						->get();
    	return view('broadcast.broadcast-customer',['data'=>$broadcastcustomer]);
    }

/* end class broadcast controller */    	
}
