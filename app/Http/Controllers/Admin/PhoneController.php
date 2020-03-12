<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\User;
use App\PhoneNumber;
use App\UserLog;

use App\Http\Controllers\OrderController;

use Excel,DateTime,Hash,Validator,Auth,Carbon,Mail,DB;

class PhoneController extends Controller
{ 
    public function index(){
      // return view('admin.list-phone.index',['phone_numbers'=>$phone_numbers]);
      return view('admin.list-phone.index');
    }

    public function load_phone(Request $request){
      $phone_numbers = PhoneNumber::
                  orderBy('created_at','desc')
                  ->get();

      $arr['view'] = (string) view('admin.list-phone.content')
                        ->with('phone_numbers',$phone_numbers);
    
      return $arr;
    }

}
