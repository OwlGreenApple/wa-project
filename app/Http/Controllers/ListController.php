<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\Customer;

class ListController extends Controller
{
    public function addList(Request $request)
    {
    	$list = new UserList;
    	$list->user_id = Auth::id();
    	$list->name = $request->name;
    	$list->save();

    	if($list->save() == true){
    		return redirect('home')->with('status','List has been created');
    	} else {
    		return redirect('home')->with('status','Error!, failed to create list');
    	}
    }

    /* User product list */
    public function userList()
    {
    	$id_user = Auth::id();
    	$userlist = UserList::where('user_id','=',$id_user)->get();
    	return view('list.user-list',['data'=>$userlist]);
    }

    public function userCustomer($id_list)
    {
        $customer = Customer::where('list_id','=',$id_list)->get();
        return view('list.user-customer',['data'=>$customer]);
    }
}
