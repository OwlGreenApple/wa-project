<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        $directory = $_SERVER['DOCUMENT_ROOT'].'/ckfinder/'.$user_name.'-'.$id;

        /* Create folder for ckfinder and ckeditor image / files */
       
        if(!isset($_SESSION['editor_path'])){
            $_SESSION['editor_path'] = '/ckfinder/'.$user_name.'-'.$id;
        }

        if(!file_exists($directory))
        {
            mkdir($directory, 0741);
        }

        $user = User::where('id','=',$id)->first();
        return view('home',['user'=>$user]);
    }

    public function updateUser(Request $request){
        $user = User::where('id','=',Auth::id())->update(
            [
                'name'=> $request->name,
                'wa_number'=>$request->wa_number,
                'api_key'=>$request->api_key,
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
