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
            'wa_number'=>['required'],
            'category'=>['required','numeric'],
        );
        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        $wa_number = $request->wa_number;
        $is_event = $request->category;

        $error = array(
            'wa_number'=>$err->first('wa_number'),
            'category'=>$err->first('category'),
        );


        $checkEvent = $this->checkEvent($is_event);
        if($checkEvent == false ){
            $error['isevent'] = 'Please do not change category value';
        } else {
            $error['isevent'] = '';
        }

        /*$checkwa = $this->checkWANumber($wa_number);
        if($checkwa == false ){
            $error['wa_check_number'] = 'Sorry, this number has been used by other user';
        }*/

        /* convert array to object */
        $err_object = (object)$error;

        if($validator->fails() || $checkEvent == false){
            return redirect('createlist')->with('error',$err_object);
        } else {
            return $next($request);
        }
        
    }

    /* To prevent if user change value number of event and message */
    private function checkEvent($is_event){
        if($is_event == 0 || $is_event == 1){
            return true;
        } else {
            return false;
        }
    }

    /* To prevent if user use another number */
    private function checkWANumber($wa_number){

        $userid = Auth::id();
        $list = UserList::where([['user_id',$userid],['wa_number','=',$wa_number]])->first();

        if(is_null($list)){
            return true;
        } else {
            return false;
        }
    }

}
