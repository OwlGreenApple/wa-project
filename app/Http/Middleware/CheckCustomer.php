<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Rules\CheckWANumbers;
use App\Customer;
use App\UserList;
use App\Additional;
use App\Rules\TelegramNumber;
use App\Rules\SubscriberEmail;
use App\Rules\SubscriberUsername;
use App\Rules\SubscriberPhone;
use Session;

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
        // Build POST request:
        Session::reflash();
        if(env('APP_ENV') == 'production')
        { 
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = env('GOOGLE_RECAPTCHA_SECRET_KEY');
            $recaptcha_response = $request->recaptcha_response;

            // Make and decode POST request:
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);
            
            // Take action based on the score returned:
            if ($recaptcha->score >= 0.5) {
                // Verified - send email
            } else {
                // Not verified - show form error
                $error['captcha'] = 'Error Captcha';
                return response()->json($error);
            }
        }
    
        /* Get all data from request and then fetch it in array */
        $req = $request->all();
        $telegram_number = $request->phone;
        $id_list = $request->listid;

        try{
          $id_list = decrypt($id_list);
        }catch(DecryptException $e){
          $error['main'] = 'Please do not change default value';
          return response()->json($error);
        }

          /* concat wa number so that get the correct number */
         $data = array(
            'name'=>$req['subscribername'],
            'email'=>$req['email'],
         );

         $rules = [
            'name'=> ['required','min:4','max:190'],
            'email'=> ['required','email','max:190',new SubscriberEmail($id_list)],
         ];

        if($request->selectType == 'ph') {
           $data['phone'] = $req['phone'];
           $rules['phone'] = ['required','min:9','max:18',new TelegramNumber, new SubscriberPhone($id_list)];
        }

        if($request->selectType == 'tl') {
           $data['usertel'] = $req['usertel'];
           $rules['usertel'] = ['required','max:190',new SubscriberUsername($id_list)];
        }

        $validator = Validator::make($data,$rules);

        if($validator->fails()){
            $error = $validator->errors();
            $data = array(
                'name'=>$error->first('name'),
                'email'=>$error->first('email'),
            );

            if($request->selectType == 'ph') {
               $data['phone'] = $error->first('phone');
            }

            if($request->selectType == 'tl') {
               $data['usertel'] = $error->first('usertel');
            }

            return response()->json($data);
        }

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
        
        return $next($request);
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
