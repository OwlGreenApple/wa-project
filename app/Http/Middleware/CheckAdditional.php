<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;

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
        $date_event = $request->date_event;
        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $today = Carbon::now()->format('Y-m-d h:i');
        $error = array();
        $data['error'] = true;
        $data['additionalerror'] = false;

        if(empty($label) || $label == null)
        {
            $error['label'] = 'List name cannot be empty';
        } 

        if(strlen($label) > 30)
        {
            $error['label'] = 'List name cannot greater than 30 characters';
        }

        if($date_event !== null && $date_event < $today)
        {
            $error['date_event'] = 'Date and time event cannot be less than today';
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
               
            #field
            foreach($fields as $row)
            {
                if(empty($row['field']))
                {
                    $data['additionalerror'] = true;
                    $data['message'] = 'Field cannot be empty';
                    return response()->json($data);
                }

                # maximum character length
                if(strlen($row['field']) > 20){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Maximum character length is 20';
                    return response()->json($data);
                }

                # default value
                if($row['field'] == 'name' || $row['field'] == 'wa_number'){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Sorry, name and wa_number has set as default';
                    return response()->json($data);
                }
            }

            if(count($fields_array) !== count($fields_filter))
            {
                $data['additionalerror'] = true;
                $data['message'] = 'Field value cannot be same';
                return response()->json($data);
            }
        }

            #dropdown
        if($dropfields!== null)
        {
            $dropdown_array = array_column($dropfields,'field');
            $dropdown_filter = array_unique($dropdown_array);

            foreach($dropfields as $rows)
            {
                if(empty($rows['field']))
                {
                    $data['additionalerror'] = true;
                    $data['message'] = 'Field cannot be empty';
                    return response()->json($data);
                }

                 # maximum character length
                if(strlen($rows['field']) > 20){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Maximum character length is 20';
                    return response()->json($data);
                }

                 # default value
                if($rows['field'] == 'name' || $rows['field'] == 'wa_number'){
                    $data['additionalerror'] = true;
                    $data['message'] = 'Sorry both of name and wa_number has set as default';
                    return response()->json($data);
                }
            }

            
            if(count($dropdown_array) !== count($dropdown_filter))
            {
                $data['additionalerror'] = true;
                $data['message'] = 'Field value cannot be same';
                return response()->json($data);
            }

        }

        #both dropdown and field
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
                $data['message'] = 'Field value cannot be same';
                return response()->json($data);
            }
        }
        return $next($request);
    }


/* End check additional */
}
