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
        $old_day = $request->old_day;
        $old_time = $request->oldtime;
        $day = $request->day;

        $rules = array(
            'hour'=>['required','date_format:H:i'],
            'message'=>['required','max:65000']
        );

        if(isset($_POST['day'])){
            $rules['day'] = ['numeric','min:-90','max:-1'];
            $get_day = $this->getDay($request->campaign_id,$old_day,$day);
        }
        else 
        {
            $day = 0;
            $get_day = $this->getDay($request->campaign_id,$old_day,$day);
        }

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails()){
            $error = array(
              'hour'=>$err->first('hour'),
              'msg'=>$err->first('message'),
              'success'=>0,
            );

            if($err->first('day') !== null)
            {
                $error['day'] = $err->first('day');
            }
            elseif($get_day == false)
            {
                $error['day'] = 'The day registered already, please choose another day!';
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

    public function getDay($campaign_id,$oldday,$new_day)
    {

       if($oldday <> $new_day)
       {
          $getday = TemplateAppointments::where([['campaign_id',$campaign_id],['days',$new_day]])->first();

          if(is_null($getday))
          {
            return true;
          }
          else
          {
            return false;
          }
       }
       else 
       {
          $getday = TemplateAppointments::where([['campaign_id',$campaign_id],['days',$oldday]])->first();
          
          if(is_null($getday))
          {
            return false;
          }
          else
          {
            return true;
          }
       }
        
    }
}
