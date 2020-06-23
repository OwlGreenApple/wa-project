<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Account;
use App\HistorySearch;
use App\User;
use App\Group;
use App\Save;
use App\Coupon;
use App\Order;
use App\PhoneNumber;
use App\Membership;
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
    $current_membership =  $user->membership;
    $user_day_left =  $user->day_left;
		$user->membership = $order->package;
    $status_upgrade = $order->status_upgrade;

		$additional_day = getAdditionalDay($order->package);
		$type_package =0;

 /*
		if(substr(,0,5) === "basic"){
			$additional_day += 30;
			// $type_package = explode("basic", $order->package)[0];
    }
		if(substr($order->package,0,10) === "bestseller"){
			$additional_day += 60;
			// $type_package = explode("bestseller", $order->package)[0];
    }
		if(substr($order->package,0,10) === "supervalue"){
			$additional_day += 90;
      // $type_package = explode("supervalue", $order->package)[0];
    }*/

    //downgrade or upgrade later
    if($status_upgrade == 2 || ($current_membership <> null && $user_day_left > 0 && $status_upgrade == 0))
    {
      $data = [
        'user_id'=>$order->user_id,
        'order_package'=>$order->package,
        'day_left'=>$user_day_left,
        'next_day'=>$additional_day,
        'eorder'=>$order,
        'euser'=>$user
      ];
      return $this->orderLater($data);
    }

    if(!is_null($phoneNumber))
    {
      $counter = getCounter($order->package);
      $max_counter = getCountMonthMessage($order->package);

      $phoneNumber->max_counter_day += $counter['max_counter_day'];
      $phoneNumber->max_counter+=$max_counter['total_message'];
      $phoneNumber->save();
    }

    if($user_day_left < 0)
    {
      $user->day_left = $additional_day;
    }
    else
    {
      $user->day_left += $additional_day;
    }
    
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

  private function orderLater(array $data)
  {
    $check_membership = Membership::where('user_id',$data['user_id'])->orderBy('id','desc')->first();

    $membership = new Membership;
    $membership->user_id = $data['user_id'];
    $membership->membership = $data['order_package'];

    if(is_null($check_membership))
    {
      $getDay = $this->updateLater($data['day_left'],$data['next_day']);
      $membership->start = $getDay['start'];
      $membership->end = $getDay['end'];
    }
    else
    {
        //if available data
      $previous_end_day = Carbon::parse($check_membership->end)->setTime(0, 0, 0);
      $next_end_day = Carbon::parse($previous_end_day)->addDays($data['next_day']);
      $membership->start = $previous_end_day;
      $membership->end = $next_end_day;
    }

    try
    {
      $membership->save();
      $arr['status'] = 'success';
      $arr['message'] = 'Order berhasil dikonfirmasi';
    }
    catch(QueryException $e)
    {
      $arr['status'] = 'error';
      $arr['message'] = $e->getMessage();
    }

    $emaildata = [
      'order' => $data['eorder'],
      'user' => $data['euser'],
    ];

    $user = $data['euser'];
    $order = $data['eorder'];

    if(env('APP_ENV') <> 'local')
    {
      Mail::send('emails.confirm-order', $emaildata, function ($message) use ($user,$order) {
        $message->from('no-reply@activrespon.com', 'Activrespon');
        $message->to($user->email);
        $message->subject('[Activrespon] Konfirmasi Order'.$order->no_order);
      });
    }

    return $arr;
  }

  //upgrade later & downgrade
  public function updateLater($day_left,$next_day)
  {
      $day_left = (int)$day_left;
      $data['start'] = Carbon::now()->setTime(0, 0, 0)->addDays($day_left);
      $data['end'] = Carbon::parse($data['start'])->addDays($next_day);
      return $data;
  }

  
/* end class */
}
