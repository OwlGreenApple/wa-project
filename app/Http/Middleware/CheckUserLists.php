<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\UserList;

class CheckUserLists
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

        $rules = array(
            'name'=>['required','max:32','unique:lists,name']
        );
        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();
        if($validator->fails()){
            return redirect('home')->with('error',$err->first('name'));
        } else {
            return $next($request);
        }
        
    }
}
