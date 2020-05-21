<?php

namespace App\Http\Controllers\Admin;
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

class WooWAController extends Controller
{   
  public function load_woowa(Request $request){
    //halaman list order admin
    $orders = Order::join(env('DB_DATABASE').'.users','orders.user_id','users.id')  
								->where('mode',1) // mode woowa
								// ->where('status_woowa',0)
                ->select('orders.*','users.email')
                ->orderBy('created_at','desc')
                ->get();
    $arr['view'] = (string) view('admin.list-woowa.content')
                      ->with('orders',$orders);
    return $arr;
  }
  

  //klo dilunasi lewat admin page
  public function confirm_order(Request $request){
    //konfirmasi pembayaran admin
    $order = Order::find($request->id);
    $order->status = 2;
    
    $user = User::find($order->user_id);
    $valid=null;
    $type = "";
    
    /*
      'Pro' => 195000, //30hari
      'Popular' => 395000, //90hari
      'Elite' => 695000, //180 hari
      'Super' => 1095000, //360 hari
    */
    if(substr($order->package,0,5) === "Pro"){
      if($order->package=='Pro Monthly'){
        $valid = $this->add_time($user,"+1 months");
      } 
      else if($order->package=='Pro Yearly'){
        $valid = $this->add_time($user,"+12 months");
      }
      else if($order->package=='Pro'){
        $valid = $this->add_time($user,"+1 months");
      }
      $type = "pro";
      $user->membership = 'pro';
    } 
    else if(substr($order->package,0,7) === "Popular"){
      $valid = $this->add_time($user,"+3 months");
      $type="popular";
      $user->membership = 'popular';
    }
    else if(substr($order->package,0,5) === "Elite"){
      if($order->package=='Elite Monthly'){
        $valid = $this->add_time($user,"+1 months");
      } else if($order->package=='Elite Yearly'){
        $valid = $this->add_time($user,"+12 months");
      }
      else if($order->package=='Elite Special 2 Months'){
        $valid = $this->add_time($user,"+2 months");
      }
      else if($order->package=='Elite Special 3 Months'){
        $valid = $this->add_time($user,"+3 months");
      }
      else if($order->package=='Elite Special 5 Months'){
        $valid = $this->add_time($user,"+5 months");
      }
      else if($order->package=='Elite Special 7 Months'){
        $valid = $this->add_time($user,"+7 months");
      }
      else if($order->package=='Elite'){
        $valid = $this->add_time($user,"+6 months");
      }
      $type = "elite";
      $user->membership = 'elite';
    }
    else if(substr($order->package,0,5) === "Super"){
      $valid = $this->add_time($user,"+12 months");
      $type="super";
      $user->membership = 'super';
    }
    
    if($valid <> null){
        $formattedDate = $valid->format('Y-m-d H:i:s');
    }

    $userlog = new UserLog;
    $userlog->user_id = $user->id;
    $userlog->type = 'membership';
    $userlog->value = $type;
    $userlog->keterangan = 'Confirm Order '.$order->package.'. From '.$user->membership.'('.$formattedDate.') to '.$type.'('.$formattedDate.')';
   // $userlog->keterangan = 'Order '.$order->package.'. From '.$user->membership.'('.$user->valid_until.') to '.$type.'('.$formattedDate.')';
    $userlog->save();

    $user->valid_until = $valid;
    $user->is_member = 1;
    $user->save();
    $order->save();

    if(substr($order->package,0,6) === "Top Up"){
      $ads = Ads::find($order->ads_id);

      $adshistory = new AdsHistory;
      $adshistory->user_id = $order->user_id;
      $adshistory->ads_id = $ads->id;
      $adshistory->credit_before = $ads->credit;
      $adshistory->credit_after = $ads->credit + $order->jmlpoin;
      $adshistory->jml_credit = $order->jmlpoin;
      $adshistory->description = 'top up';
      $adshistory->save();

      $ads->credit = $ads->credit + $order->jmlpoin;
      $ads->save();
    }

    $emaildata = [
      'order' => $order,
      'user' => $user,
    ];

    Mail::send('emails.confirm-order', $emaildata, function ($message) use ($user,$order) {
      $message->from('no-reply@omnilinkz.com', 'Omnilinkz');
      $message->to($user->email);
      $message->subject('[Omnilinkz] Konfirmasi Order'.$order->no_order);
    });

    $arr['status'] = 'success';
    $arr['message'] = 'Order berhasil dikonfirmasi';
    $arr['response'] = $this->IsPay($user->email,17,1);

    return $arr;
  }

  
  public function load_invoice(Request $request){
    //halaman list order admin
    $orders = Order::join(env('DB_DATABASE').'.users','orders.user_id','users.id')  
                ->select('orders.*','users.email')
                ->orderBy('created_at','desc')
                ->get();
    $arr['view'] = (string) view('admin.list-woowa.content')
                      ->with('orders',$orders);
    return $arr;
  }
  

/* end class */
}
