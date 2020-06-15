<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\OrderController;
use Carbon\Carbon;
use App\User;
use App\Order;
use Cookie;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;

    use AuthenticatesUsers {
        logout as performLogout;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function authenticated(Request $request,$user)
    {
        if($request->remember == 1)
        {
            $this->setCookie($request->email,$request->password);
        }
        else {
            $this->delCookie($request->email,$request->password);
        }

			/*	if ($request->ajax()) {
            Auth::loginUsingId($user->id);
						return response()->json([
								'success' => 1,
								'email' => $request->email,
						]);
				}
				else {*/
						if ( $user->is_admin  == 1) {// do your magic here
								return redirect('list-user');
						}
						if ( $user->is_admin  == 2) {// Halaman woowa
								return redirect('list-woowa');
						}
				// }
    }

    public function loginAjax(Request $request)
    {
        $email = $request->email;
        $password = $request->password;
        $namapaket = null;
        $result_upgrade = true;
        $upgrade_price = 0;

        if(session('order') <> null)
        {
          $namapaket = session('order')['namapaket'];
        }

        if(Auth::guard('web')->attempt(['email' => $email, 'password' => $password])) 
        {
            $user = Auth::user();
            if($namapaket <> null)
            {
              // return true if downgrade
              $result_upgrade = $this->checkDowngrade($namapaket,$user);
            }

            if($result_upgrade == false)
            {
               $upgrade_price = $this->getTotalCount($user,$namapaket);
            }
            
            return response()->json([
                'success' => 1,
                'email' => $request->email,
                'status_upgrade'=>$result_upgrade,
                'upgrade_price'=>number_format($upgrade_price),
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Not Credential Account'
            ]);
        }
    }

    public function getTotalCount($user,$namapaket)
    {
      $order = Order::where('user_id',$user->id)
                ->where("status",2)
                ->orderBy('created_at','desc')
                ->first();

      if (!is_null($order)) 
      {
        $current_package = $order->package;
      }
      else
      {
        $current_package = $user->membership;
      }

      $dayleft = $user->day_left;

      $package_price = getPackagePrice($namapaket);
      $oldpackage_price = getPackagePrice($current_package);

      $get_new_order_day = getAdditionalDay($namapaket);
      $get_old_order_day = getAdditionalDay($current_package);

      $order_controller = new OrderController;
      $remain_day_price = $order_controller->getUpgradeNow($package_price,$get_new_order_day,$oldpackage_price,$get_old_order_day,$dayleft);

      $upgrade_price = $package_price + round($remain_day_price);

      return $upgrade_price;
    }

    public function checkDowngrade($namapaket,$user)
    {
      $order = Order::where('user_id',$user->id)
                ->where("status",2)
                ->orderBy('created_at','desc')
                ->first();

      if (!is_null($order)) 
      {
        $current_package = $order->package;
      }
      else
      {
        $current_package = $user->membership;
      }

      $data = [
          'current_package'=>$current_package,
          'order_package'=>$namapaket,
      ];
      
      $get_status = checkMembershipDowngrade($data);
      return $get_status;
    }

    private function setCookie($email,$password)
    {
        if(!empty($email) && !empty($password))
        {
            Cookie::queue(Cookie::make('email', $email, 1440*7));
            Cookie::queue(Cookie::make('password', $password, 1440*7));
        } else {
            return redirect()->route('login');
        }
    }

    private function delCookie($cookie_email,$cookie_pass)
    {
        if(!empty($cookie_email) && !empty($cookie_pass))
        {
            Cookie::queue(Cookie::forget('email'));
            Cookie::queue(Cookie::forget('password'));
        } else {
            return redirect()->route('login');
        }
    }

    public function logout(Request $request)
    {
        // session_start();
        $this->performLogout($request);
        return redirect('/');
    }

/* end class LoginController */    
}
