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
    	$list = UserList::where('user_id',$id)->get();
    	return view('reminder.reminder',['data'=>$list]);
    }
}
