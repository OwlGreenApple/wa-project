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

use App\Helpers\Helper;
use Carbon\Carbon;
use Auth,Mail,Validator,Storage,DateTime,Crypt,Session;

class OrderController extends Controller
{   
  public function cekharga($namapaket, $price){
    //cek paket dengan harga
    $paket = array(
      'basic1' => 195000,
      'bestseller1' => 370500,
      'supervalue1' => 526500,
			
      'basic2' => 275000,
      'bestseller2' => 522500,
      'supervalue2' => 742500,
			
      'basic3' => 345000,
      'bestseller3' => 655500,
      'supervalue3' => 931500,
			
      'basic4' => 415000,
      'bestseller4' => 788500,
      'supervalue4' => 1120500,
			
      'basic5' => 555000,
      'bestseller5' => 1054500,
      'supervalue5' => 1498500,
			
      'basic6' => 695000,
      'bestseller6' => 1320500,
      'supervalue6' => 1876500,
			
      'basic7' => 975000,
      'bestseller7' => 1852500,
      'supervalue7' => 2632500,
			
      'basic8' => 1255000,
      'bestseller8' => 2384500,
      'supervalue8' => 3388500,
			
      'basic9' => 155000,
      'bestseller9' => 2954500,
      'supervalue9' => 4288500,
			
    );

    if(isset($paket[$namapaket])){
      if($price!=$paket[$namapaket]){
        return false; 
      } else {
        return true;
      }
    } else {
      return false;
    }
  }

  public function pricing(Request $request){
    return view('order.pricing');
  }

  public function checkout($id){
    //halaman checkout
		$priceupgrade = 0;
		$dayleft = 0;
		if (Auth::check()) {
			$user = Auth::user();
			$order = Order::where('user_id',$user->id)
								->where("status",2)
                ->orderBy('created_at','desc')
								->first();
			if (!is_null($order)) {
				$priceupgrade = $order->total;
			}
			$dayleft = $user->day_left;
		}
    return view('order.checkout')->with(array(
              'id'=>$id,
              'priceupgrade'=>$priceupgrade,
              'dayleft'=>$dayleft,
            ));
  }

  public function check_coupon(Request $request){
    $user = Auth::user();
    //cek kodekupon
    $arr['status'] = 'success';
    $arr['message'] = '';
    $arr['totaltitle'] = number_format($request->harga, 0, '', '.');
    $arr['total'] = $request->harga;
    $arr['diskon'] = 0;
    $arr['coupon'] = null;

    if($request->kodekupon!=''){
      $user_id = 0;
      if (!is_null($user)) {
        $user_id = $user->id;
      }
      $coupon = Coupon::where('kodekupon',$request->kodekupon)
              ->where(function($query) use ($request) {
                $query->where('package_id',$request->idpaket)
                      ->orwhere('package_id',0);
              })
              ->where(function($query) use ($user_id) {
                $query->where('user_id',$user_id)
                      ->orwhere('user_id',0);
              })
              ->first();

      if(is_null($coupon)){
        $arr['status'] = 'error';
        $arr['message'] = 'Kupon tidak terdaftar';
        return $arr;
      } else {
        // $now = new DateTime();
        // $date = new DateTime($coupon->valid_until);
        $now = Carbon::now();
        $date = Carbon::createFromFormat('Y-m-d H:i:s', $coupon->valid_until);
        
        if($date->lt($now)){
          $arr['status'] = 'error';
          $arr['message'] = 'Kupon sudah tidak berlaku';
          return $arr;
        } else {
          if($coupon->valid_to=='new' and Auth::check()){

          } else if($coupon->valid_to=='extend' and !Auth::check()){

          } 
          else if(($coupon->valid_to=='') || ($coupon->valid_to=='expired-membership') || ($coupon->valid_to=='all') ){
            $total = 0;
            $diskon = 0;

            if($coupon->diskon_value==0 and $coupon->diskon_percent!=0){
              $diskon = $request->harga * $coupon->diskon_percent/100;
              $total = $request->harga - $diskon;
            } else {
              $diskon = $coupon->diskon_value;
              $total = $request->harga - $coupon->diskon_value;
            }

            $arr['status'] = 'success';
            $arr['message'] = 'Kupon berhasil dipakai & berlaku sekarang';
            $arr['totaltitle'] = number_format($total, 0, '', '.');
            $arr['total'] = $total;
            $arr['diskon'] = $diskon;
            $arr['coupon'] = $coupon;
            return $arr;
          }
        }
      }
    }

    return $arr;
  }

	public function check_upgrade(Request $request){
		$arr['status'] = 'success';
		$arr['message'] = 'Check upgrade success';

		$priceupgrade = 0;
		$dayleft = 0;
		if (Auth::check()) {
			$user = Auth::user();
			$order = Order::where('user_id',$user->id)
								->where("status",2)
                ->orderBy('created_at','desc')
								->first();
			if (!is_null($order)) {
				$priceupgrade = $order->total;
			}
			$dayleft = $user->day_left;
		}
		if ($request->price - $priceupgrade<0) {
			$arr['status'] = 'error';
			$arr['message'] = 'Cannot Downgrade';
			return $arr;
		}
		
		return $arr;
	}
	
	//order dengan register
  public function submit_checkout_register(Request $request) {
		// ditaruh ke session dulu
    $stat = $this->cekharga($request->namapaket,$request->price);

    $pathUrl = str_replace(url('/'), '', url()->previous());
    if($stat==false){
      // return redirect("checkout/1")->with("error", "Paket dan harga tidak sesuai. Silahkan order kembali.");
      return redirect($pathUrl)->with("error", "Paket dan harga tidak sesuai. Silahkan order kembali.");
    }

    $arr = $this->check_coupon($request);
    if($arr['status']=='error'){
      // return redirect("checkout/1")->with("error", $arr['message']);
      return redirect($pathUrl)->with("error", $arr['message']);
    }

		$month = 1;
		if(substr($request->namapaket,0,5) === "basic"){
			$month = 1;
    }
		if(substr($request->namapaket,0,10) === "bestseller"){
			$month = 2;
    }
		if(substr($request->namapaket,0,10) === "supervalue"){
			$month = 3;
    }

    $order = array(
      "price"=>$request->price,
      "namapaket"=>$request->namapaket,
      "namapakettitle"=>$request->namapakettitle,
      "coupon_code"=>$request->kupon,
      "idpaket" => $request->idpaket,
      "month" => $month,
    );

    if(session('order') == null)
    {
      session(['order'=>$order]);
    }
    
    return redirect('register');
  }

  //checkout klo uda login
  public function submit_checkout(Request $request){
    //buat order user lama
    $stat = $this->cekharga($request->namapaket,$request->price);

    $pathUrl = str_replace(url('/'), '', url()->previous());
    if($stat==false){
      // return redirect("checkout/1")->with("error", "Paket dan harga tidak sesuai. Silahkan order kembali.");
      return redirect($pathUrl)->with("error", "Paket dan harga tidak sesuai. Silahkan order kembali.");
    }

    $arr = $this->check_upgrade($request);
    if($arr['status']=='error'){
      // return redirect("checkout/1")->with("error", $arr['message']);
      return redirect($pathUrl)->with("error", $arr['message']);
    }

		$month = 1;
		if(substr($request->namapaket,0,5) === "basic"){
			$month = 1;
    }
		if(substr($request->namapaket,0,10) === "bestseller"){
			$month = 2;
    }
		if(substr($request->namapaket,0,10) === "supervalue"){
			$month = 3;
    }

    $diskon = 0;
    // $total = $request->price;
    $kuponid = null;
    if($request->kupon!==''){
      $arr = $this->check_coupon($request);

      if($arr['status']=='error'){
        return redirect($pathUrl)->with("error", $arr['message']);
      } else {
        $diskon = $arr['diskon'];
        
        if($arr['coupon']!=null){
          $kuponid = $arr['coupon']->id;
        }
      }
    }
		
    $user = Auth::user();

		$data = [
			"user"=> $user,
			"namapaket"=> $request->namapaket,
			"kuponid"=> $kuponid,
			"price"=> $request->price,
			"priceupgrade"=> $request->priceupgrade,
			"diskon"=> $diskon,
			"namapakettitle"=> $request->namapakettitle,
      "phone"=>$user->phone_number,
			"month"=> $month,
		];
		
		$order = Order::create_order($data);
    return view('order.thankyou')->with(array(
              'order'=>$order,    
            ));
  }

  public function thankyou(Request $request){
    return view('order.thankyou')->with(array(
              'order'=>null,    
            ));
  }
  

  public function index_order(){
    //halaman order user
    return view('order.index');
  }
  
  //upload bukti TT 
  public function confirm_payment_order(Request $request){
    $user = Auth::user();
    //konfirmasi pembayaran user
    $order = Order::find($request->id_confirm);
    $folder = $user->email.'/buktibayar';

    if($order->status==0)
    {
      $order->status = 1;

      if($request->hasFile('buktibayar'))
      {
        // $path = Storage::putFile('bukti',$request->file('buktibayar'));
        $dir = 'bukti_bayar/'.explode(' ',trim($user->name))[0].'-'.$user->id;
        $filename = $order->no_order.'.jpg';
        Storage::disk('s3')->put($dir."/".$filename, file_get_contents($request->file('buktibayar')), 'public');
        $order->buktibayar = $dir."/".$filename;
        
      } else {
        // $arr['status'] = 'error';
        // $arr['message'] = 'Upload file buktibayar terlebih dahulu';
        // return $arr;
        $pathUrl = str_replace(url('/'), '', url()->previous());
        return redirect($pathUrl)->with("error", "Upload file buktibayar terlebih dahulu");
      }  
      $order->keterangan = $request->keterangan;
      $order->save();

      // $arr['status'] = 'success';
      // $arr['message'] = 'Konfirmasi pembayaran berhasil';
    } else {
      // $arr['status'] = 'error';
      // $arr['message'] = 'Order telah atau sedang dikonfirmasi oleh admin';
        $pathUrl = str_replace(url('/'), '', url()->previous());
        return redirect($pathUrl)->with("error", "Order telah atau sedang dikonfirmasi oleh admin.");
    }

    // return $arr;
    return view('order.thankyou-confirm-payment');
  }

  public function load_order(Request $request){
    //halaman order user
    $orders = Order::where('user_id',Auth::user()->id)
                ->orderBy('created_at','desc')
                ->paginate(15);
                //->get();
    $arr['view'] = (string) view('order.content')
                      ->with('orders',$orders);
    $arr['pager'] = (string) view('order.pagination')
                      ->with('orders',$orders); 
    return $arr;
  }

/* end class */
}
