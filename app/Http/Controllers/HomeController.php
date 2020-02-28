<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;
use App\PhoneNumber;
use App\UserList;
use App\Customer;
use App\Campaign;
use App\Reminder;
use App\ReminderCustomers;
use App\BroadCast;
use App\BroadCastCustomers;
use DB;

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
        //session_start();
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

        $lists = UserList::where('user_id',$id)->get()->count();
        $campaign = Customer::where('user_id',$id)->get()->count();
        $contact = Customer::where('user_id',$id)->get()->count();

        $latest_list = DB::select('select * from lists where user_id ='.$id.' and DATE(created_at) > (NOW() - INTERVAL 7 DAY)');
        (count($latest_list) > 0)? $latest = '+'.count($latest_list) : $latest = count($latest_list);

        $reminder = ReminderCustomers::where('user_id','=',$id)->get()->count();

        $reminder_sent = ReminderCustomers::where([['user_id','=',$id],['status',1]])->get()->count();

        $broadcast = BroadCast::where('broad_casts.user_id','=',$id)
            ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')->get()->count();

        $broadcast_sent = BroadCast::where([['broad_casts.user_id','=',$id],['broad_cast_customers.status',1]])
            ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')->get()->count();

        $total_message = $reminder + $broadcast;
        $total_sending_message = $reminder_sent + $broadcast_sent;

        $data = array(
          'lists'=>$lists,
          'latest_lists'=>$latest,
          'campaign'=>$campaign,
          'contact'=>$contact,
          'total_message'=>$total_message,
          'total_sending_message'=>$total_sending_message
        );

        return view('home',$data);
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

    public function checkPhone(){
      $userid = Auth::id();
      $phone = PhoneNumber::where('user_id',$userid)->get();

      if($phone->count() < 1)
      {
        $data['status'] = 1;
      }
      else {
        $data['status'] = 0;
      }

      return response()->json($data);
    }

    /* test */
    public function historyOrder()
    {
      return view('auth.history-order');
    }

/* end class HomeController */
}
