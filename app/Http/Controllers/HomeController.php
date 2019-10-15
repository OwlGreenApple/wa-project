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

    public function getScanBarcode()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wassenger.com/v1/devices/5d6e15906de1a4001c90a0f4/scan?force=true",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "token: 717c449cac6613abd70349cbd889b4955523292e7a45c49ebb2880b9b77e944d44f467389e75a080"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }

/* end class HomeController */
}
