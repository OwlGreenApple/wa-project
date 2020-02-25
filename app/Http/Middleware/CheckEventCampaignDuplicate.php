<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckDateEvent;

class CheckEventCampaignDuplicate
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
        $rules = [
          'campaign_name'=>['required','max:50'],
          'event_time'=>['required', new CheckDateEvent],
        ];
        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $error = $validator->errors();
            return response()->json([
              'campaign_name'=>$error->first('campaign_name'),
              'event_time'=>$error->first('event_time'),
              'success'=>0,
            ]);
        }

        return $next($request);
    }
}
