<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Account;
use App\HistorySearch;
use App\User;
use App\Group;
use App\Save;
use App\Coupon;
use App\Invoice;
use App\InvoiceOrder;
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
    //halaman list order admin woowa
    $orders = Order::join(env('DB_DATABASE').'.users','orders.user_id','users.id')  
								->where('mode',1) // mode woowa
								->where('status',2) // paid
								// ->where('status_woowa',0)
                ->select('orders.*','users.email')
                ->orderBy('created_at','desc')
                ->orderBy('status_woowa','asc')
                ->get();
		$totaltagihan=0;
		foreach ($orders as $order) {
			$totaltagihan += ($order->grand_total / $order->month);
		}
    $arr['view'] = (string) view('admin.list-woowa.content')
                      ->with([
												'orders'=>$orders,
												'totaltagihan'=>$totaltagihan,
											]);
    return $arr;
  }


  public function create_invoice(Request $request){
		$invoice = new Invoice;
    $str = 'I'.$dt->format('ymdHi');
    $invoice_number = Order::autoGenerateID($order, 'no_invoice', $str, 3, '0');
    $invoice->no_invoice = $invoice_number;
    $invoice->status = 0;
    $invoice->buktibayar = "";
    $invoice->keterangan = "";
    $invoice->total = 0;
		$invoice->save();

    //halaman list order admin woowa
    $orders = Order::join(env('DB_DATABASE').'.users','orders.user_id','users.id')  
								->where('mode',1) // mode woowa
								->where('status',2) // paid
								// ->where('status_woowa',0)
                ->select('orders.*','users.email')
                ->orderBy('created_at','desc')
                ->orderBy('status_woowa','asc')
                ->get();
		$totaltagihan=0;
		foreach ($orders as $order) {
			$totaltagihan += ($order->grand_total / $order->month);

			$invoiceorder = new InvoiceOrder;
			$invoiceorder->invoice_id = $invoice_id;
			$invoiceorder->order_id = $order->id;
			$invoiceorder->save();
		}

    $invoice->total = $totaltagihan;
		$invoice->save();

    $arr['status'] = 'success';
    $arr['message'] = 'Invoice berhasil dibuat';

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
