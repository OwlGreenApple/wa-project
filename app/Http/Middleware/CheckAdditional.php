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
    public function handle($request, Closure $next)
    {

        $label = $request->list_label;
        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $data['error'] = true;
        $data['additionalerror'] = false;
        $checkuserlist = new CheckUserLists;
        $error = array();

        if(empty($label) || $label == null)
        {
            $error['label'] = 'List name cannot be empty';
        } 

        if(strlen($label) > 50)
        {
            $error['label'] = 'List name cannot greater than 50 characters';
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
