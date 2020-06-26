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
use App\Message;
use App\Helpers\Helper;
use Crypt;
use Carbon\Carbon;
use Auth,Mail,Validator,Storage,DateTime;

use App\Jobs\SendNotif;

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
		$phoneNumber = PhoneNumber::where("user_id",$order->user_id)->first();					
		$user = User::find($order->user_id);
    $user_day_left = $user->day_left;

		$additional_day = getAdditionalDay($order->package);
		$type_package =0;

    //downgrade or upgrade
    $data = [
      'user_id'=>$order->user_id,
      'order_package'=>$order->package,
      'package_day'=>$additional_day,
    ];
    
    //to save order into memberships
    $status_upgrade = $this->orderLater($data);

    if($status_upgrade <> 'error')
    {
      $order->status = 2;
      $order->save();
    }
    else
    {
      $arr['status'] = 'error';
      $arr['message'] = 'Error, please contact programmer!';
      return $arr;
    }

    if(!is_null($phoneNumber))
    {
      $counter = getCounter($order->package);
      $max_counter = getCountMonthMessage($order->package);

      if($status_upgrade['status'] == 0)
      {
         $phoneNumber->max_counter_day = $counter['max_counter_day'];
      }
     
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

    if($status_upgrade['status'] == 0)
    {
      $user->membership = $order->package;
    }

    $user->status = 1;
    $user->save();

    $emaildata = [
      'order' => $order,
      'user' => $user,
    ];

    if(env('APP_ENV') <> 'local')
    {
      $message = null;
      $message .= "*Selamat ".$user->name.",* \n\n";
      $message .= "Pembelianmu sudah *berhasil di proses*, _kamu bisa langsung gunakan akun *Activrespon*-mu sekarang juga._ \n \n";

      $message .= "Jika ada yang perlu ditanyakan seputar *Activrespon*, jangan ragu menghubungi support kami di \n";
      $message .= "*WA 0817-318-368* \n\n";

      $message .= 'Terima Kasih,'."\n\n";
      $message .= 'Team Activrespon'."\n";
      $message .= '_*Activrespon is part of Activomni.com_';

      // SendNotif::dispatch($user->phone_number,$message,env('REMINDER_PHONE_KEY'));
      $message_send = Message::create_message($user->phone_number,$message,env('REMINDER_PHONE_KEY'));
      
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
      $getDay = $this->updateLater($data['package_day']);
      $membership->start = $getDay['start'];
      $membership->end = $getDay['end'];
      $membership->status = 0;
    }
    else
    {
        //if available data
      $previous_package = $check_membership->membership;
      $new_package = $data['order_package'];
      $previous_end_day = Carbon::parse($check_membership->end)->setTime(0, 0, 0);
      $next_end_day = Carbon::parse($previous_end_day)->addDays($data['package_day']);
      $status_upgrade = $this->checkDowngrade($previous_package,$new_package);

      if($status_upgrade == 0)
      {
        $membership->status = 0;
      }
      $membership->start = $previous_end_day;
      $membership->end = $next_end_day;
      $membership->status_upgrade = $status_upgrade;
    }

    try
    {
      $membership->save();
      $status_upgrade = $membership->status_upgrade;
      $arr['status'] = $status_upgrade;
    }
    catch(QueryException $e)
    {
      $arr['status'] = 'error';
    }

    return $arr;
  }

  //upgrade later & downgrade
  public function updateLater($package_day)
  {
      $package_day = (int)$package_day;
      $data['start'] = Carbon::now()->setTime(0, 0, 0);
      $data['end'] = Carbon::parse($data['start'])->addDays($package_day);
      return $data;
  }

  public function checkDowngrade($previous_package,$new_package)
    {
      $data = [
          'current_package'=>$previous_package,
          'order_package'=>$new_package,
      ];
      
      $get_status = checkMembershipDowngrade($data);

      if($get_status == true)
      {
        $status_upgrade = 1;
      }
      else
      {
        $status_upgrade = 0;
      }

      return $status_upgrade;
    }

  
/* end class */
}
