<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        session_start();
        $id = Auth::id();
        $user_name = Auth::user()->name;
        $folder = $user_name.'-'.$id;

        /*if (env('APP_ENV') == 'local'){
          $directory = public_path().'/ckeditor/'.$folder;
        }
        else {
          $directory = 'home2/activwa/public_html/ckfinder/'.$folder;
        }*/

        $directory = public_path().'/ckeditor/'.$folder;

        if(!file_exists($directory))
        {
            mkdir($directory, 0755,true);
            //$path = $directory;
            //File::makeDirectory($path, $mode = 0741, true, true);
        }

        $user = User::where('id','=',$id)->first();
        return view('home',['user'=>$user]);
    }

    public function updateUser(Request $request){
        $user = User::where('id','=',Auth::id())->update(
            [
                'name'=> $request->name,
                'password'=>Hash::make($request->password)
            ]
        );

        if($user == true){
            return redirect('home')->with('message','Your data has been updated successfully');
        } else {
            return redirect('home')->with('message','Error!,Your data failed to update');
        }
    }
}
