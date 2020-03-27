<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Rules\CheckAppointmentDay;
use App\TemplateAppointments;

class CheckEditAppointmentTemplate
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
        // dd($request->all());
        $get_day = true;
        $rules = array(
            'hour'=>['required','date_format:H:i'],
            'message'=>['required','max:65000']
        );

        if(isset($_POST['day'])){
            $day = $request->day;
            $rules['day'] = ['numeric','min:-90','max:-1',new CheckAppointmentDay($request->campaign_id,$day)];
        }
        else 
        {
            $day = 0;
            $get_day = $this->getDay($request->campaign_id,$day);
        }

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails()){
            $error = array(
              'hour'=>$err->first('hour'),
              'msg'=>$err->first('message'),
              'success'=>0,
            );

            if($get_day == false)
            {
                $error['day'] = 'The day registered already, please choose another day!';
            }
            else 
            {
                $error['day'] = $err->first('day');
            }
            
            return response()->json($error);
        }


        //DOUBLE CODE TO AVOID IF VALIDATOR FAILS ASSUMED IF PASS
        if($get_day == false)
        {
            $error['day'] = 'The day registered already, please choose another day!';
            $error['success'] = 0;
            return response()->json($error);
        }

        return $next($request);
    }

    public function getDay($campaign_id,$day)
    {
       $getday = TemplateAppointments::where([['campaign_id',$campaign_id],['days',$day]])->first();

        if(!is_null($getday))
        {
          return false;
        }
        else
        {
          return true;
        }
    }
}
