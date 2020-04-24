<?php

namespace App\Http\Middleware;

use App\PhoneNumber;
use App\User;
use Illuminate\Support\Facades\Auth;
use Closure;

class AuthSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $iduser = Auth::id();
      $phone = PhoneNumber::where('user_id',$iduser)->get();

      if($phone->count() < 1){
        return redirect('settings');
      }

      return $next($request);
    }
}
