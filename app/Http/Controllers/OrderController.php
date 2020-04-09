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
  public function pricing(Request $request){
    return view('order.pricing');
  }

  public function checkout($id){
    //halaman checkout
    return view('order.checkout')->with(array(
              'id'=>$id,
            ));
  }

  public function check_coupon($kodekupon,$harga,$idpaket){
    $user = Auth::user();
    //cek kodekupon
    $arr['status'] = 'success';
    $arr['message'] = '';
    $arr['total'] = number_format($harga, 0, '', '.');
    $arr['diskon'] = 0;
    $arr['coupon'] = null;

    if($kodekupon!=''){
      $user_id = 0;
      if (!is_null($user)) {
        $user_id = $user->id;
      }
      $coupon = Coupon::where('kodekupon',$kodekupon)
              ->where(function($query) use ($idpaket) {
                $query->where('package_id',$idpaket)
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
          else if(substr($coupon->valid_to,0,7)=='package'){
            $total = 0;
            $diskon = 0;
            $paket = "";
            $paketid = 0;
            $dataPaket = "";

            if ($coupon->valid_to == "package-elite-2") {
              $total = 195000;
              $paket = "Paket Special Elite 2 Bulan";
              $paketid = 12;
              $dataPaket = "Elite Special 2 Months";
            }
            if ($coupon->valid_to == "package-elite-3") {
              $total = 295000;
              $paket = "Paket Special Elite 3 Bulan";
              $paketid = 13;
              $dataPaket = "Elite Special 3 Months";
            }
            if ($coupon->valid_to == "package-elite-5") {
              $total = 395000;
              $paket = "Paket Special Elite 5 Bulan";
              $paketid = 14;
              $dataPaket = "Elite Special 5 Months";
            }
            if ($coupon->valid_to == "package-elite-12") {
              $total = 495000;
              $paket = "Paket Special Elite 12 Bulan";
              $paketid = 15;
              $dataPaket = "Elite Special 12 Months";
            }
            
            // selectbox ditambah dengan paket kupon 
            $arr['status'] = 'success-paket';
            $arr['message'] = 'Kupon berhasil dipakai & berlaku sekarang';
            $arr['total'] = number_format($total, 0, '', '.');
            $arr['diskon'] = $diskon;
            $arr['coupon'] = $coupon;
            $arr['kodekupon'] = $coupon->kodekupon;
            $arr['paket'] = $paket;
            $arr['paketid'] = $paketid;
            $arr['dataPaket'] = $dataPaket;
            $arr['dataPrice'] = $total;
            return $arr;
          }
          else if(($coupon->valid_to=='') || ($coupon->valid_to=='expired-membership') || ($coupon->valid_to=='all') ){
            $total = 0;
            $diskon = 0;

            if($coupon->diskon_value==0 and $coupon->diskon_percent!=0){
              $diskon = $harga * $coupon->diskon_percent/100;
              $total = $harga - $diskon;
            } else {
              $diskon = $coupon->diskon_value;
              $total = $harga - $coupon->diskon_value;
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


  public function thankyou(Request $request){
    return view('order.thankyou');
  }
  

  
/* end class */
}
