<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\Http\Middleware\CheckUserLists;

class CheckAdditional
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    function __construct()
    {
        // LANGUAGE ERROR
        $this->label_name_empty = 'Label name cannot be empty';
        $this->label_phone_empty = 'Label phone cannot be empty';
        $this->label_email_empty = 'Label email cannot be empty';
        $this->label_name_gt = 'Label name cannot greater than 30 characters';
        $this->label_phone_gt = 'Label phone cannot greater than 30 characters';
        $this->label_email_gt = 'Label email cannot greater than 30 characters';
    }

    public function handle($request, Closure $next)
    {
        $label_name = $request->label_name;
        $label_phone = $request->label_phone;
        $label_email = $request->label_email;
        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $data['error'] = true;
        $data['additionalerror'] = false;
        $checkuserlist = new CheckUserLists;
        $error = array();

        if($label_name == null)
        {
            $error['label_name'] = $this->label_name_empty;
        }

        if(strlen($label_name) > 30)
        {
            $error['label_name'] = $this->label_name_gt;
        }

        if($label_phone == null)
        {
            $error['label_phone'] = $this->label_phone_empty;
        }

        if(strlen($label_phone) > 30)
        {
            $error['label_phone'] = $this->label_phone_gt;
        }

        if($label_email == null)
        {
            $error['label_email'] = $this->label_email_empty;
        }

        if(strlen($label_email) > 30)
        {
            $error['label_email'] = $this->label_email_gt;
        }

        if(count($error) > 0)
        {
            $error['error'] = true;
            return response()->json($error);
        }

        if($fields!== null)
        {
            $fields_array = array_column($fields, 'field');
            $fields_filter = array_unique($fields_array);
               
            //field
            foreach($fields as $row)
            {
                if(empty($row['field']))
                {
                    $data['additionalerror'] = true;
                    $data['message'] = 'Additional field cannot be empty';
                    return response()->json($data);
                }

                // maximum character length
                if(strlen($row['field']) > 20){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Maximum character length is 20';
                    return response()->json($data);
                }

                // default value
                if($row['field'] == 'subscribername' || $row['field'] == 'email' || $row['field'] == 'phone' || $row['field'] == 'usertel'){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Sorry, subscribername, email, phone, usertel has set as default';
                    return response()->json($data);
                }
            }

            if(count($fields_array) !== count($fields_filter))
            {
                $data['additionalerror'] = true;
                $data['message'] = 'Additional field value cannot be same';
                return response()->json($data);
            }
        }

        //dropdown
        if($dropfields!== null)
        {
            $dropdown_array = array_column($dropfields,'field');
            $dropdown_filter = array_unique($dropdown_array);

            foreach($dropfields as $rows)
            {
                if(empty($rows['field']))
                {
                    $data['additionalerror'] = true;
                    $data['message'] = 'Additional dropdown field cannot be empty';
                    return response()->json($data);
                }

                // maximum character length
                if(strlen($rows['field']) > 20){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Additional dropdown maximum character length is 20';
                    return response()->json($data);
                }

                // default value
                 if($rows['field'] == 'subscribername' || $rows['field'] == 'email' || $rows['field'] == 'phone' || $rows['field'] == 'usertel'){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Sorry, subscribername, email, phone, usertel has set as default';
                    return response()->json($data);
                }
            }

            
            if(count($dropdown_array) !== count($dropdown_filter))
            {
                $data['additionalerror'] = true;
                $data['message'] = 'Additional dropdown field value cannot be same';
                return response()->json($data);
            }

        }

        //both dropdown and field
        if($fields!== null && $dropfields!== null)
        {
            $fields_array = array_column($fields, 'field');
            $fields_filter = array_unique($fields_array);

            $dropdown_array = array_column($dropfields,'field');
            $dropdown_filter = array_unique($dropdown_array);

            $merge_array = array_merge($fields_array,$dropdown_array);
            $filter_array = array_unique($merge_array);

            if((count($fields_array) !== count($fields_filter)) || (count($dropdown_array) !== count($dropdown_filter)) || (count($merge_array) !== count($filter_array)) )
            {
                $data['additionalerror'] = true;
                $data['message'] = 'Additional field value cannot be same';
                return response()->json($data);
            }
        }
        return $next($request);
    }

     /* To allow function if value equal wih user id 
    private function checkBotAPI($bot_api){
        $userid = Auth::id();
        $getlist = UserList::where('bot_api','=',$bot_api)->first();

        if(is_null($getlist))
        {
           return true;
        }

        $botuserid = $getlist->user_id;
        if($botuserid == $userid){
            return true;
        } else {
            return false;
        }
    }
    */

/* End check additional */
}
