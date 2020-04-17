<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Rules\CheckPlusCode;
use App\Rules\CheckCallCode;
use App\Rules\InternationalTel;
use App\Rules\AvailablePhoneNumber;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegisteredEmail;
use App\Order;
use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        /*$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = env('GOOGLE_RECAPTCHA_SECRET_KEY');
        $recaptcha_response = $data['recaptcha_response'];

        // Make and decode POST request:
        $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        $recaptcha = json_decode($recaptcha);
        
        // Take action based on the score returned:
        if ($recaptcha->score >= 0.5) {
            // Verified - send email
        } else {
            // Not verified - show form error
            $error['error_phone'] = 'Error Captcha';
            return response()->json($error);
        }*/

        return Validator::make($data, [
            'username' => ['required','string','max:255'],
            'email' => ['required','string', 'email', 'max:255', 'unique:users'],
            'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            'phone' => ['required','max:18','min:6','max:18',new InternationalTel,new AvailablePhoneNumber($data['code_country'])]
            //'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $generated_password = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'),0,10);
        $name = $data['username'];

        $user = User::create([
          'name' => $data['username'],
          'email' => $data['email'],
          'phone_number'=>$data['code_country'].$data['phone'],
          'password' => Hash::make($generated_password),
          'gender'=>$data['gender']
        ]);
           
        if(env('APP_ENV') <> 'local')
        {
          Mail::to($data['email'])->send(new RegisteredEmail($generated_password));
        }

        return $user;
    }

    public function register(Request $request)
    {   
        $signup = $this->create($request->all());
        $order = null;

        if(session('order') <> null)
        {
          $diskon = 0;
          $kuponid = null;
          $process_order = new OrderController;
          $stat = $process_order->cekharga( session('order')['namapaket'],session('order')['price']);

          $pathUrl = str_replace(url('/'), '', url()->previous());
          if($stat==false)
          {
            return redirect($pathUrl)->with("error", "Paket dan harga tidak sesuai. Silahkan order kembali.");
          }

          if(session('order')['coupon_code'] <> null)
          {
              $arr = $process_order->check_coupon($request->session()->get('order'));

              if($arr['status']=='error')
              {
                return redirect($pathUrl)->with("error", $arr['message']);
              } 
              else 
              {
                $diskon = $arr['diskon'];
                
                if($arr['coupon']!=null){
                  $kuponid = $arr['coupon']->id;
                }
              }
          }

          $data = [
            "user"=> $signup,
            "namapaket"=> session('order')['namapaket'],
            "kuponid"=> $kuponid,
            "price"=> session('order')['price'],
            "diskon"=> $diskon,
            "namapakettitle"=> session('order')['namapakettitle'],
          ];
      
          $order = Order::create_order($data);
          Auth::loginUsingId($signup->id);
          return redirect('thankyou');
        }
        else
        {
          Auth::loginUsingId($signup->id);
          return redirect('home');
        }

    }

/* END CONTROLLER */
}
