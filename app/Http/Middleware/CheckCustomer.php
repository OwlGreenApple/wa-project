<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckWANumbers;
use App\Customer;

class CheckCustomer
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
        $product_list = $request->session()->get('userlist');
        $request->session()->reflash();

        /* Get all data from request and then fetch it in array */
         $req = $request->all();

         /* Avoid customer fill 0 as a leading number on wa number */
         if(!preg_match('/^[1-9][0-9]*$/',$req['wa_number'])){
            $error['wa_number'] = 'Please do not use 0 as first number';
            return response()->json($error);
         }

         /* concat wa number so that get the correct number */
         $wa_number = $req['code_country'].$req['wa_number'];
         $data = array(
            'name'=>$req['name'],
            'code_country'=>$req['code_country'],
            'wa_number'=>$wa_number,
         );

         $rules = [
            'name'=> ['required','min:4','max:190'],
            'code_country'=>['required','numeric'],
            'wa_number'=> ['required',new CheckWANumbers,'digits_between:5,15'],
        ];

        $validator = Validator::make($data,$rules);

        if($validator->fails()){
            $error = $validator->errors();
            $data = array(
                'name'=>$error->first('name'),
                'wa_number'=>$error->first('wa_number'),
                'code_country'=>$error->first('code_country'),
            );
            return response()->json($data);
        } else {
            return $next($request);
        }
    }
}
