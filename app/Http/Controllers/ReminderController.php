<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\Reminder;


class ReminderController extends Controller
{
    public function index(){
    	$id = Auth::id();
    	$list = Reminder::where('reminders.user_id',$id)
    			->join('lists','reminders.list_id','=','lists.id')
    			->select('lists.name','reminders.*')
    			->get();
    	return view('reminder.reminder',['data'=>$list]);
    }

    public function createReminder(){
    	$id = Auth::id();
    	$list = UserList::where('user_id',$id)->get();
    	return view('reminder.reminder-create',['data'=>$list]);
    }

      public function addReminder(Request $request){
    	$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
    	$days = $req['day'];

    	foreach($req['id'] as $row=>$list_id){
    		$reminder = new Reminder;
    		$reminder->user_id = $user_id;
    		$reminder->list_id = $list_id;
    		$reminder->days = $days;
    		$reminder->message = $message;
    		$reminder->save();
    	}

    	if($reminder->save() == true){
    		return redirect('remindercreate')->with('status','Your message has been created');
    	} else {
    		return redirect('remindercreate')->with('status_error','Your message failed to create');
    	}
    }
}
