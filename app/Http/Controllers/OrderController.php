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
  public function cekharga($namapaket, $price){
    //cek paket dengan harga
    $paket = array(
      'basic1' => 155000,
      'bestseller1' => 195000,
      'supervalue1' => 1020000,
			
      'basic2' => 155000,
      'bestseller2' => 195000,
      'supervalue2' => 1020000,
			
      'basic3' => 155000,
      'bestseller3' => 195000,
      'supervalue3' => 1020000,
			
      'basic4' => 155000,
      'bestseller4' => 195000,
      'supervalue4' => 1020000,
			
      'basic5' => 155000,
      'bestseller5' => 195000,
      'supervalue5' => 1020000,
			
      'basic6' => 155000,
      'bestseller6' => 195000,
      'supervalue6' => 1020000,
			
      'basic7' => 155000,
      'bestseller7' => 195000,
      'supervalue7' => 1020000,
			
      'basic8' => 155000,
      'bestseller8' => 195000,
      'supervalue8' => 1020000,
			
      'basic9' => 155000,
      'bestseller9' => 195000,
      'supervalue9' => 1020000,
			
      'basic10' => 155000,
      'bestseller10' => 195000,
      'supervalue10' => 1020000,
			
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
    return view('order.checkout')->with(array(
              'id'=>$id,
            ));
  }

  public function check_coupon(Request $request){
    $user = Auth::user();
    //cek kodekupon
    $arr['status'] = 'success';
    $arr['message'] = '';
    $arr['total'] = number_format($request->harga, 0, '', '.');
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
            $arr['total'] = number_format($total, 0, '', '.');
            $arr['diskon'] = $diskon;
            $arr['coupon'] = $coupon;
            return $arr;
          }
        }
      }
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

    return view('auth.register')->with(array(
      "price"=>$request->price,
      "namapaket"=>$request->namapaket,
      "coupon_code"=>$request->kupon,
      "idpaket" => $request->idpaket,
    ));
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

    if(substr($request->namapaket,0,6) === "Top Up"){
      $ads = Ads::where('user_id',Auth::user()->id)->first();
      if(is_null($ads)){
        // return redirect("checkout/5")->with("error", "Buat Ads terlebih dahulu sebelum melakukan Top Up.");   
        return redirect($pathUrl)->with("error", "Buat Ads terlebih dahulu sebelum melakukan Top Up.");   
      } 
    }

    $diskon = 0;
    // $total = $request->price;
    $kuponid = null;
    if($request->kupon!=''){
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
			"diskon"=> $diskon,
		]
		
		$order = Order::create_order($data);

    return view('pricing.thankyou')->with(array(
              'order'=>$order,    
            ));
  }

  public function thankyou(Request $request){
    return view('order.thankyou');
  }
  

  
/* end class */
}
