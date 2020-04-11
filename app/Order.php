<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;

use Carbon, Mail, DB;

class Order extends Model
{
  protected $table = 'orders';
  protected $connection = 'mysql2';

	public function create_order($data){
    //unique code 
    $unique_code = mt_rand(1, 1000);

    $dt = Carbon::now();
    $order = new Order;
    $str = 'OML'.$dt->format('ymdHi');
    $order_number = $this->autoGenerateID($order, 'no_order', $str, 3, '0');
    $order->no_order = $order_number;
    $order->user_id = $user->id;
    $order->package =$request->namapaket;
    $order->jmlpoin=0;
    $order->coupon_id = $kuponid;
    $order->total = $request->price + $unique_code;
    $order->discount = $diskon;
    $order->grand_total = $request->price - $diskon + $unique_code;
    $order->status = 0;
    $order->buktibayar = "";
    $order->keterangan = "";
    $order->save();

    if($order->grand_total!=0){
      //mail order to user 
      $emaildata = [
          'order' => $order,
          'user' => $user,
          'nama_paket' => $request->namapaket,
          'no_order' => $order_number,
      ];
      Mail::send('emails.order', $emaildata, function ($message) use ($user,$order_number) {
        $message->from('no-reply@activrespon.com', 'Activrespon');
        $message->to($user->email);
        $message->subject('[Activrespon] Order Nomor '.$order_number);
      });
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
