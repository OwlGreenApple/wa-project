<?php

namespace App\Http\Middleware;

use Closure;

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

        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $data['error'] = true;

        $fields_array = array_column($fields, 'field');
        $fields_filter = array_unique($fields_array);

        $dropdown_array = array_column($dropfields,'field');
        $dropdown_filter = array_unique($dropdown_array);

        $merge_array = array_merge($fields_array,$dropdown_array);
        $filter_array = array_unique($merge_array);
       
        #field
        foreach($fields as $row)
        {
            if(empty($row['field']))
            {
                $data['message'] = 'Field cannot be empty';
                return response()->json($data);
            }

            # maximum character length
            if(strlen($row['field']) > 20){
                $data['message'] = 'Maximum character length is 20';
                return response()->json($data);
            }

            # default value
            if($row['field'] == 'name' || $row['field'] == 'wa_number'){
                $data['message'] = 'Sorry, name and wa_number has set as default';
                return response()->json($data);
            }
        }

        #dropdown
        foreach($dropfields as $rows)
        {
            if(empty($rows['field']))
            {
                $data['message'] = 'Field cannot be empty';
                return response()->json($data);
            }

             # maximum character length
            if(strlen($rows['field']) > 20){
                $data['message'] = 'Maximum character length is 20';
                return response()->json($data);
            }

             # default value
            if($rows['field'] == 'name' || $rows['field'] == 'wa_number'){
                $data['message'] = 'Sorry both of name and wa_number has set as default';
                return response()->json($data);
            }
        }

        if((count($fields_array) !== count($fields_filter)) || (count($dropdown_array) !== count($dropdown_filter)) || (count($merge_array) !== count($filter_array)) )
        {
            $data['message'] = 'Field value cannot be same';
            return response()->json($data);
        }
        return $next($request);
    }
}
