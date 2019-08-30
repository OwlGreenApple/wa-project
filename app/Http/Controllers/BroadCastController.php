<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Customer;
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

    	if($broadcast->save() == true){
    		return redirect('broadcastform')->with('status','Your message has been created');
    	} else {
    		return redirect('broadcastform')->with('status_error','Your message failed to create');
    	}
    }

    /* Display user list to broadcast */
    public function displayBroadCastList($id_broadcast){
    	$id_user = Auth::id();
    	$userlist = UserList::where('user_id','=',$id_user)->get();
    	Session::flash('id_broadcast', $id_broadcast);
    	return view('broadcast.broadcast-list-customer',['data'=>$userlist]);
    }

    public function sendBroadCast(Request $request){
    	/* Get broadcast message */
    	$id_broadcast = Session::get('id_broadcast');
    	Session::reflash();
    	$broadcast = BroadCast::where('id',$id_broadcast)->first();
    	$broadcast_message = $broadcast->message;
    	/* Get List Id */
    	$idlist = $request->all();
    	/* Get customer id */

    	//print("<pre>".print_r($idlist)."</pre>");

    	foreach($idlist as $id=>$val){
    		$customer = Customer::where('list_id',$val)->get();
    		foreach($customer as $cid){
    			$broadcast_customer = new BroadCastCustomers;
	    		$broadcast_customer->list_id = $val;
	    		$broadcast_customer->customer_id = $cid->id;
	    		$broadcast_customer->message = $broadcast_message;
	    		$broadcast_customer->save();
    		}/*end inner foreach*/
    	}/*end outer foreach*/

    	if($broadcast_customer->save() == true){
    		echo 'Broadcast';
    	} else {
    		echo 'Failed';
    	}
    }	
}
