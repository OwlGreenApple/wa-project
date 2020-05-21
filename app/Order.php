<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;

use Carbon\Carbon;
use Mail, DB, Session;

class Order extends Model
{
	/*
	* status 
	* 0 => created
	* 1 => confirmed bukti transfer, waiting admin response
	* 2 => paid
	*
	*	Mode 
	* 0 => simi
	* 1 => woowa
	*
	*	status_woowa 
	* 0 => not paid or full paid to woowa
	* 1 => paid
	*
	* if package not basic -> order is more than 1 month
	*/
  protected $table = 'orders';
  protected $connection = 'mysql2';

	public static function create_order($data){
    //unique code 
    $unique_code = mt_rand(1, 1000);
		$user = $data['user'];

    $dt = Carbon::now();
    $order = new Order;
    $str = 'ACT'.$dt->format('ymdHi');
    $order_number = Order::autoGenerateID($order, 'no_order', $str, 3, '0');
    $order->no_order = $order_number;
    $order->user_id = $user->id;
    $order->package =$data['namapaket'];
    $order->package_title =$data['namapakettitle'];
    $order->coupon_id = $data['kuponid'];
    $order->total = $data['price'] + $unique_code;
    $order->total_upgrade = $data['priceupgrade'];
    $order->discount = $data['diskon'];
    $order->grand_total = $data['price'] + $data['priceupgrade'] - $data['diskon'] + $unique_code;
    $order->status = 0;
    $order->buktibayar = "";
    $order->keterangan = "";
    $order->status_woowa = 0;
    $order->mode = 0;
    $order->month = $data['month'];
    $order->save();

    if($order->grand_total!=0){
      //mail order to user 
      $emaildata = [
          'order' => $order,
          'user' => $user,
          'nama_paket' => $data['namapaket'],
          'no_order' => $order_number,
      ];

      if(env('APP_ENV') <> 'local')
      {
          Mail::send('emails.order', $emaildata, function ($message) use ($user,$order_number) {
            $message->from('no-reply@activrespon.com', 'Activrespon');
            $message->to($user->email);
            $message->subject('[Activrespon] Order Nomor '.$order_number);
          });
      }
      //delete session order
      session::forget('order');
    } 
    else {
			// for freemium case
    }
	}
	
  public static function autoGenerateID($model, $field, $search, $pad_length, $pad_string = '0')
  {
    $tb = $model->select(DB::raw("substr(".$field.", ".strval(strlen($search)+1).") as lastnum"))
								->whereRaw("substr(".$field.", 1, ".strlen($search).") = '".$search."'")
								->orderBy('id', 'DESC')
								->first();
		if ($tb == null){
			$ctr = 1;
		}
		else{
			$ctr = intval($tb->lastnum) + 1;
		}
		return $search.str_pad($ctr, $pad_length, $pad_string, STR_PAD_LEFT);
  }
}
