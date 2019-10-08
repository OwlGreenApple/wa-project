<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Rules\CheckWANumbers;
use App\Customer;
use App\UserList;
use App\Additional;

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
         $wa_number = $request->wa_number;
         $id_list = $request->listid;

          try{
             $id_list = decrypt($id_list);
          }catch(DecryptException $e){
              $error['wa_number'] = 'Please do not change default value';
              return response()->json($error);
          }
       

         if(!is_numeric($wa_number)){
            $error['wa_number'] = 'Please use valid numbers';
            return response()->json($error);
         }

         $wa_number = '+62'.$request->wa_number;

         if($this->checkList($req['listname']) == false){
            $error['list'] = 'Please do not modify list name';
            return response()->json($error);
         }


         if(isset($req['data']) && $this->checkAdditional($req['data'],$id_list) !== true)
         {
            $result = $this->checkAdditional($req['data'],$id_list);
            $error['data'] = json_decode($result,true);
             return response()->json($error);
         }

         /* Avoid customer fill 0 as a leading number on wa number */
         if(!preg_match('/^[1-9][0-9]*$/',$req['wa_number'])){
            $error['wa_number'] = 'Please do not use 0 or +';
            return response()->json($error);
         } 

         if(preg_match('/^62[0-9]/',$req['wa_number'])){
            $error['wa_number'] = 'Please do not use 62 as first number';
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

    public function checkList($listname){
        $check_link = UserList::where([
            ['name','=',$listname],
            ['status','=',1],
        ])->first();

        if(empty($listname)){
            return false;
        } elseif(is_null($check_link)) {
            return false;
        } else {
            return true;
        }
    }

    public function checkAdditional($data,$list_id){
        $error = array();
        if(count($data) > 0)
        {
            foreach($data as $name=>$val)
            {
                $value[] = $val;
                $fieldname[] = $name;
                $is_optional = Additional::where([['list_id',$list_id],['is_optional',1]])->whereIn('name',$fieldname)->get();
            }
        } else {
            return true;
        }

        if($is_optional->count() > 0)
        {
             foreach($is_optional as $row)
             {

                if(empty($data[$row->name])){
                     $error[$row->name] = "Column ".$row->name." cannot be empty ";
                } else {
                    return true;
                }
             }
            return json_encode($error);
        } else {
            return true;
        }

    }

/* end middleware */    
}
