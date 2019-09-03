<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Customer;
use Carbon\Carbon;
use App\User;
use Session;

use App\ReminderCustomers;
use App\Reminder;

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

    /* Display broadcast data */
    public function FormBroadCast(){
    	$id_user = Auth::id();
    	$userlist = UserList::where('user_id','=',$id_user)->get();
    	return view('broadcast.broadcast-form',['data'=>$userlist]);
    }

    /* Create broadcast list */
    public function createBroadCast(Request $request){
    	$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];

    	foreach($req['id'] as $row=>$list_id){
    		$broadcast = new BroadCast;
    		$broadcast->user_id = $user_id;
    		$broadcast->list_id = $list_id;
    		$broadcast->message = $message;
    		$broadcast->save();
    	}

    	/* if successful inserted data broadcast into database then this run */
    	if($broadcast->save() == true){
    		
    		foreach($req['id'] as $row=>$list_id){
    			/* retrieve customer id */
    			$customer = Customer::where([
    				['list_id','=',$list_id],
    				['status','=',1],
    			])->get();
    			/* retrieve broadcast id according on created at */
    			$created_date = $broadcast->created_at;
    			$broadcast_get_id = BroadCast::where([
    				['list_id','=',$list_id],
    				['created_at','=',$created_date],
    			])->select('id')->get();
    			/* insert into broadcast customer */
    			foreach($customer as $col){
    				foreach($broadcast_get_id as $id_broadcast){
    					$broadcastcustomer = new BroadCastCustomers;
			    		$broadcastcustomer->user_id = $user_id;
			    		$broadcastcustomer->list_id = $list_id;
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

    /* put this function on cron job later */
    public function testBroadCast(){
    	/* Users counter */
		$user = User::select('id','counter')->get();
		foreach($user as $userow){
			$id_user = $userow->id;
			$count = $userow->counter;
			$broadcast_customers = BroadCastCustomers::where([
				['status','=',0],
				['user_id','=',$id_user],
			])->orderBy('id','asc');

			/* Broadcast */
			if($broadcast_customers->count() > 0){
	            /* get user id where status = 0 asc */
	            $id_broadcast = $broadcast_customers->take($count)->get();
	            foreach($id_broadcast as $id){
	            	  //some code to call wassenger here....
	            	  $update_broadcast = BroadCastCustomers::where('id',$id->id)->update(['status'=>1]);
	            	  if($update_broadcast == true){
						  	$count = $count - 1;
						  	User::where('id',$id_user)->update(['counter'=>$count]);
					  } else {
						  	echo 'Error!! Unable to update broadcast customer';
					  }
					  
	            }
	        } else {
	        	/* Reminder */
	        	$reminder_customers = ReminderCustomers::where([
                    ['user_id','=',$id_user],
                    ['status','=',0],
                ])->orderBy('id','asc');

               /* get days from reminder */
                $reminder = Reminder::where('reminders.user_id','=',$id_user)
                				->rightJoin('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
                				->where('reminder_customers.status','=',0)
                				->rightJoin('customers','customers.id','=','reminder_customers.customer_id')
                				->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.days','reminders.created_at as datecr','customers.created_at AS cstreg')
                				->take($count)
                				->get();

                /* check date reminder customer and update if succesful sending */
                foreach($reminder as $col) {
                    $date_reminder = Carbon::parse($col->datecr); //date when reminder was created
                    $day_reminder = $col->days; // how many days
                    $customer_signup = Carbon::parse($col->cstreg);
                    $adding = $customer_signup->addDays($day_reminder);
                    //$reminder_customer_status = $col->rc_st;
                    $reminder_customers_id = $col->rcs_id;

                    if($adding >= $date_reminder){
                    	//some code to call wassenger here....
                    	ReminderCustomers::where([
                    		['id',$reminder_customers_id],
                    		['status','=',0],
                    	])->update(['status'=>1]);
                    	 // cut user's wa bandwith
                        $count = $count - 1;
                        User::where('id',$id_user)->update(['counter'=>$count]);
                    }
	        	}

		}
		}
    }

    public function justcarbon(){
    	echo Carbon::parse('2019-08-28 03:49:44')->addDays(3);
    }

/* end class broadcast controller */    	
}
