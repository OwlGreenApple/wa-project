<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use App\Rules\OldPassword;
use App\Rules\TelegramNumber;

class CheckSettings
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
            'user_name' => ['required','string','max:255'],
            'user_phone' => ['required','max:18',new TelegramNumber],
        ];

        if(!empty($request->oldpass) && !empty($request->confpass) || !empty($request->newpass))
        {
          $rules = [
            'oldpass' => ['required','string', new OldPassword],
            'confpass' => ['required','string', 'min:8', 'max:32'],
            'newpass' => [ 'required','string', 'min:8', 'max:32', 'same:confpass'],
            'timezone' => ['required', 'timezone', 'max:60']
          ];
        }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $err = $validator->errors();
            $error = array(
              'status'=>'error',
              'user_name'=>$err->first('user_name'),
              'user_phone'=>$err->first('user_phone'),
              'oldpass'=>$err->first('oldpass'),
              'confpass'=>$err->first('confpass'),
              'newpass'=>$err->first('newpass'),
              'timezone'=>$err->first('timezone'),
            );

            return response()->json($error);
        }

        return $next($request);
    }
}
