<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Sender;

class SenderController extends Controller
{
    public function addSender(Request $request){
    	$req = $request->all();
    	$wa_number = $req['wa_number'];
    	$api_key = $request->api_key;

    	foreach($wa_number as $rows){
    		$sender = new Sender;
	    	$sender->user_id = Auth::id();
	    	$sender->api_key = $request->api_key;
	    	$sender->wa_number = $rows;
	    	$sender->save();
    	}

    	if($sender->save() == true){
    		return redirect('home')->with('status','Your sender has been created');
    	} else {
    		return redirect('home')->with('error','Error! Unable to create sender');
    	}
    }
}
