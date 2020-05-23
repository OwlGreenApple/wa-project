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
use App\PhoneNumber;
use App\Helpers\Helper;
use Crypt;
use Carbon\Carbon;
use Auth,Mail,Validator,Storage,DateTime;

class OrderController extends Controller
{   
  public function load_list_order(Request $request){
    //halaman list order admin
    $orders = Order::join(env('DB_DATABASE').'.users','orders.user_id','users.id')  
                ->select('orders.*','users.email')
                ->orderBy('created_at','desc')
                ->get();
		// dd($orders);
    $arr['view'] = (string) view('admin.list-order.content')
                      ->with('orders',$orders);
    /*$arr['pager'] = (string) view('admin.list-order.pagination')
                      ->with('orders',$orders); */
    return $arr;
  }
  

  //klo dilunasi lewat admin page
  public function confirm_order(Request $request){
    //konfirmasi pembayaran admin
    $order = Order::find($request->id);
    $order->status = 2;
    $order->save();
    
		$phoneNumber = PhoneNumber::where("user_id",$order->user_id)->first();					
		$user = User::find($order->user_id);
		$user->membership = $order->package;
    
		$additional_day = 0;
		$type_package =0;

		if(substr($order->package,0,5) === "basic"){
			$additional_day += 30;
			// $type_package = explode("basic", $order->package)[0];
    }
		if(substr($order->package,0,10) === "bestseller"){
			$additional_day += 90;
			// $type_package = explode("bestseller", $order->package)[0];
    }
		if(substr($order->package,0,10) === "supervalue"){
			$additional_day += 180;
      // $type_package = explode("supervalue", $order->package)[0];
    }

    if(!is_null($phoneNumber))
    {
      $type_package = substr($order->package,-1,1);
      if ($type_package=="1") {
        $phoneNumber->max_counter_day=1000;
        $phoneNumber->max_counter=15000;
      }
      if ($type_package=="2") {
        $phoneNumber->max_counter_day=1500;
        $phoneNumber->max_counter=25000;
      }
      if ($type_package=="3") {
        $phoneNumber->max_counter_day=2000;
        $phoneNumber->max_counter=40000;
      }
      if ($type_package=="4") {
        $phoneNumber->max_counter_day=2500;
        $phoneNumber->max_counter=60000;
      }
      if ($type_package=="5") {
        $phoneNumber->max_counter_day=3000;
        $phoneNumber->max_counter=90000;
      }
      if ($type_package=="6") {
        $phoneNumber->max_counter_day=3500;
        $phoneNumber->max_counter=130000;
      }
      if ($type_package=="7") {
        $phoneNumber->max_counter_day=4000;
        $phoneNumber->max_counter=190000;
      }
      if ($type_package=="8") {
        $phoneNumber->max_counter_day=4500;
        $phoneNumber->max_counter=250000;
      }
      if($type_package=="9") {
        $phoneNumber->max_counter_day=5000;
        $phoneNumber->max_counter=330000;
      }
      $phoneNumber->save();
    }

    $user->day_left = $additional_day;
    $user->membership = $order->package;
    $user->status = 1;
    $user->save();

    $emaildata = [
      'order' => $order,
      'user' => $user,
    ];

    if(env('APP_ENV') <> 'local')
    {
      Mail::send('emails.confirm-order', $emaildata, function ($message) use ($user,$order) {
        $message->from('no-reply@activrespon.com', 'Activrespon');
        $message->to($user->email);
        $message->subject('[Activrespon] Konfirmasi Order'.$order->no_order);
      });
    }

    $arr['status'] = 'success';
    $arr['message'] = 'Order berhasil dikonfirmasi';
    return $arr;
  }

  
/* end class */
}
