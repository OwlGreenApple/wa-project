<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
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

        if ( $user->is_admin  == 1) {// do your magic here
            return redirect('list-user');
        }
        if ( $user->is_admin  == 2) {// Halaman woowa
            // return redirect('list-user');
        }
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
        session_start();
        $this->performLogout($request);
        return redirect()->route('visitor');
    }

/* end class LoginController */    
}
