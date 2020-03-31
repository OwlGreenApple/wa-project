<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use App\Rules\InternationalTel;
use App\Rules\CheckCallCode;
use App\Rules\CheckPlusCode;
use App\Rules\AvailablePhoneNumber;

class CheckCallingCode
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
            'code_country' => ['required',new CheckPlusCode,new CheckCallCode],
            'phone_number' => ['required','min:6','max:18',new InternationalTel,new AvailablePhoneNumber($request->code_country)]
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $err = $validator->errors();
            $error = array(
              'status'=>'error',
              'phone_number'=>$err->first('phone_number'),
              'code_country'=>$err->first('code_country'),
            );

            return response()->json($error);
        }

        return $next($request);
    }
}
