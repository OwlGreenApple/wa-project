<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;

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
        $listid = $request->list_id;
        $date_send = $request->date_send;
        $hour = $request->hour;
        $message = $request->message;

        if(!empty($listid))
        {
          'list_id'=>['required'],
        }

        $rules = [
          'campaign_name'=>['required','max:50'],
          'date_send'=>['required','max:50'],
        ];
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $error = $validator->errors();
            return response()->json([
              'campaign_name'=>$error->first('campaign_name'),
              'success'=>0,
            ]);
        }
        return $next($request);
    }
}
