<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Account;
use App\HistorySearch;
use App\User;
use App\Group;
use App\Save;
use App\Coupon;
use App\Order;
use App\UserLog;
use App\Notification;
use App\Ads;
use App\AdsHistory;

use App\Helpers\Helper;
use Carbon, Crypt;
use Auth,Mail,Validator,Storage,DateTime;

class OrderController extends Controller
{   
  public function pricing(Request $request){
    return view('order.pricing');
  }
  

  
/* end class */
}
