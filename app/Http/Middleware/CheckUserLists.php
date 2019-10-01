<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\UserList;
use App\Sender;
use App\Rules\CheckDateEvent;

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

        $wa_number = $request->wa_number;
        $is_event = $request->category;

        if(isset($request->date_event)){
            $date_event = $request->date_event;
        } else {
            $date_event = null;
        }
        
        $today = Carbon::now()->format('Y-m-d h:i');

        if(!isset($request->wa_number)){
            return redirect('createlist')->with('error_number','Please, register your WhatsApp Number first');
        }

        if($this->checkEvent($is_event) == false ){
            return redirect('createlist')->with('isevent','Please do not change category value');
        } 

        $checkwa = $this->checkWANumber($wa_number);
        if($checkwa == false ){
            return redirect('createlist')->with('wa_check_number','Sorry, this number is not yours');
        } 

        if($is_event == 1 && empty($date_event)){
             return redirect('createlist')->with('date_event','Please fill date for event');
        } 

        if($date_event < $today && $is_event == 1){
            return redirect('createlist')->with('date_event','Date event cannot be less with today');
        } 
       
        return $next($request);
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
        $getlist = Sender::where([['user_id',$userid],['wa_number','=',$wa_number]])->first();

        if(!is_null($getlist)){
            return true;
        } else {
            return false;
        }
    }

}
