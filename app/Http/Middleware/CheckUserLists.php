<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
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
            //'event_date'=>['required',new CheckDateEvent],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        $wa_number = $request->wa_number;
        $is_event = $request->category;
        $date_event = $request->date_event;

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

         if($is_event == 1 && empty($date_event)){
            $error['date_event'] = 'Please fill date for event';
            $date_event = false;
        } else {
            $error['date_event'] = '';
            $date_event = true;
        }

        $today = Carbon::now()->format('Y-m-d h:i');
        if($date_event < $today){
            $error['date_event'] = 'Date event cannot be less with today';
            $date_event = false;
        } else {
            $error['date_event'] = '';
            $date_event = true;
        }

        /*$checkwa = $this->checkWANumber($wa_number);
        if($checkwa == false ){
            $error['wa_check_number'] = 'Sorry, this number has been used by another users';
        } else {
            $error['wa_check_number'] = '';
        }
        */

        /* convert array to object */
        $err_object = (object)$error;

        if($validator->fails() || $checkEvent == false || $date_event == false){
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

    /* To prevent if user use another number 
    private function checkWANumber($wa_number){

        $userid = Auth::id();
        $getlistid = Sender::where([['user_id',$userid],['wa_number','=',$wa_number]])->first()->id;

        if(is_null($getlist)){
            return true;
        } else {
            return false;
        }
    }*/

}
