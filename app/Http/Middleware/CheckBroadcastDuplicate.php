<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckValidListID;
use App\Rules\CheckBroadcastDate;

class CheckBroadcastDuplicate
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
        $campaign_name = $request->campaign_name;
        $date_send = $request->date_send;
        $hour = $request->hour;
        $message = $request->message;
  
        $rules = array(
          'campaign_name'=>['required','max:50'],
          'date_send'=>['required',new CheckBroadcastDate],
          'hour'=>['required'],
          'message'=>['max:4095'],
        );

        if(isset($_POST['list_id']))
        {
           $rules['list_id'] = ['required', new CheckValidListID];
        } 

        if(isset($_POST['group_name']))
        {
           $rules['group_name'] = ['required', 'max:50'];
        }

        if(isset($_POST['channel_name']))
        {
           $rules['channel_name'] = ['required', 'max:50'];
        }

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $error = $validator->errors();
            $data_error = [
              'list_id' =>$error->first('list_id'),
              'group_name' =>$error->first('group_name'),
              'channel_name' =>$error->first('channel_name'),
              'campaign_name'=>$error->first('campaign_name'),
              'date_send'=>$error->first('date_send'),
              'hour'=>$error->first('hour'),
              'message'=>$error->first('message'),
              'success'=>0,
            ];

            return response()->json($data_error);
        }
        return $next($request);
    }
}
