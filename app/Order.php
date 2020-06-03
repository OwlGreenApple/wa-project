<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Order;
use App\Helpers\ApiHelper;

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
	* 1 => invoice generated
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
    $grand_total = $data['price'] + $data['priceupgrade'] - $data['diskon'] + $unique_code;

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
    $order->grand_total = $grand_total;
    // $order->grand_total = $data['price'] + $data['priceupgrade'] - $data['diskon'] + $unique_code;
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

      // WA MESSAGE
      $phone = $data['phone'];
      $message = null;
      $message .= '*Hi '.$user->name."\n";
      $message .= 'Terima kasih sudah membeli Activrespon.'."\n";
      $message .= '_Berikut ini adalah invoice Anda:_'."\n"."\n";
      $message .= '*Tgl Pembelian :* '.$dt->format('d-M-Y').''."\n";
      $message .= '*No Invoice :* '.$order_number.''."\n";
      $message .= '*Jumlah :*  Rp. '.str_replace(",",".",number_format($grand_total))."\n";
    /*  $message .= '*Nama :* '.$user->name.''."\n";
      $message .= '*Status Order :* Pending'."\n";
      $message .= 'Anda telah memesan Paket '.$data['namapaket'].''."\n"."\n";

      $message .= '*Rp. '.number_format($data['price'] + $unique_code).'*'."\n";
      if($data['priceupgrade'] > 0)
      {
        $message .= '*Upgrade Price :*'.number_format($data['priceupgrade'])."\n";
      }
      $message .= '*Diskon :* Rp.'. number_format($data['diskon'])."\n";
      $message .= '*Total :* Rp.'. number_format($grand_total)."\n"."\n";
      */
      $message .= '*Harap transfer persis sesuai invoice*'."\n";
      $message .= '_(dengan kode uniknya)_'."\n";
      $message .= 'agar mempercepat proses konfirmasi'."\n\n";

      $message .= '*Silahkan Transfer ke :*'."\n"."\n";
      $message .= 'BCA (Sugiarto Lasjim)'."\n";
      $message .= '8290-812-845'."\n\n";
      // $message .= 'Sugiarto Lasjim'."\n"."\n";
      $message .= '*Sesudah transfer:*'."\n";
      $message .= '- *Login* ke https://activrespon.com'."\n";
      $message .= '- *Klik* Profile'."\n";
      $message .= '- Pilih *Order & Confirm*'."\n";
      $message .= '- *Upload bukti konfirmasi* disana'."\n\n";

      $message .= 'Terima Kasih,'."\n\n";
      $message .= 'Team Activrespon'."\n";
      $message .= '_*Activrespon is part of Activomni.com_';

      // ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');
			ApiHelper::send_simi($phone,$message,env('REMINDER_PHONE_KEY'));

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
