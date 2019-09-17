<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use App\Rules\CheckWANumbers;
use App\Customer;
use App\UserList;

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
    
        /* Get all data from request and then fetch it in array */
         $req = $request->all();
         $wa_number = '+62'.$request->wa_number;

         /* Avoid customer fill 0 as a leading number on wa number */
         if(!preg_match('/^[1-9][0-9]*$/',$req['wa_number'])){
            $error['wa_number'] = 'Please do not use 0 as first number';
            return response()->json($error);
         } 

         if(preg_match('/^[62][0-9]*$/',$req['wa_number'])){
            $error['wa_number'] = 'Please do not use 62 as first number, just use number after 0 or +62';
            return response()->json($error);
         }

        if($this->checkwanumbers($wa_number,$req['listname']) == false){
            $error['wa_number'] = 'Sorry, this number has already been taken..';
            return response()->json($error);
         }
        
         /* concat wa number so that get the correct number */
         //$wa_number = $req['code_country'].$req['wa_number'];
         $data = array(
            'name'=>$req['name'],
            'code_country'=>$req['code_country'],
            'wa_number'=>$wa_number,
         );

         $rules = [
            'name'=> ['required','min:4','max:190'],
            'code_country'=>['required'],
            'wa_number'=> ['required','between:5,16'],
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

     public function checkwanumbers($wa_number,$listname){
        $get_id_list = UserList::where('name','=',$listname)->first();
        $id_user_list = $get_id_list->id;

        $checkwa = Customer::where([
                    ['wa_number','=',$wa_number],
                    ['list_id','=',$id_user_list]
                    ])->first();

        if(is_null($checkwa)){
            return true;
        } else {
            return false;
        }
    }

/* end middleware */    
}
