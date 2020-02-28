<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Validator;
use Closure;

class CheckImportCSV
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
        $ext = $request->file('csv_file')->getClientOriginalExtension();

        if($ext <> 'csv')
        {
            return response()->json(['message'=>'File must be .csv extension']);
        }

        $validator = Validator::make($request->all(),[
            'csv_file' =>['required','max:500']
        ]);

        if($validator->fails())
        {
            $err = $validator->errors();
            return response()->json(['message'=>$err->first('csv_file')]);
        }
        return $next($request);
    }
}