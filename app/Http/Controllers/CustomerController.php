<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\UserList;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index(Request $request, $product_list){
    	$check_link = UserList::where('name','=',$product_list)->first();
    	if(empty($product_list)){
    		return redirect('/');
    	} elseif(is_null($check_link)) {
    		return redirect('/');
    	} else {
    		$request->session()->flash('userlist',$product_list);
    		return view('register-customer');
    	}
    }

    public function addCustomer(Request $request){
    	$userlist =  $request->session()->get('userlist'); //retrieve session from userlist
    	$get_id_list = UserList::where('name','=',$userlist)->first();

    	$wa_number = $request->code_country.$request->wa_number;
    	$customer = new Customer;
    	$customer->list_id = $get_id_list->id;
    	$customer->name = $request->name;
    	$customer->wa_number = $wa_number;
    	$customer->save();

    	if($customer->save() == true){
    		$data['success'] = true;
    		$data['message'] = 'Thank You For Join Us';
    	} else {
    		$data['success'] = false;
    		$data['message'] = 'Ups! Sorry there is something wrong with our system';
    	}
    	return response()->json($data);
    }
}
