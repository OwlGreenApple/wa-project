<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;

class AdminController extends Controller
{
    public function index()
    {
    	$user = User::where('is_admin',0)->get();
    	return view('admin.admin',['data'=>$user]);
    }

    public function LoginUser($id){
    	Auth::loginUsingId($id, true);
    	return redirect('home');
    }

    public function importCSVPage()
    {
        return view('admin.importcsv');
    }

    /* BE CAREFUL IF YOU PERFORM IMPORT USING THIS FUNCTION IT WOULD RETURN ALL DATA TO LIST_ID = 1 */
    public function importCustomerCSV(Request $request){
        $file = $request->file('csv_file');
        Excel::import(new UsersImport(1), $file);
    }
}
