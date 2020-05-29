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
use App\Config;
use App\Server;
use App\UserList;
use App\Customer;
use App\Campaign;
use App\Reminder;
use App\ReminderCustomers;
use App\BroadCast;
use App\BroadCastCustomers;
use DB;
use Carbon\Carbon;

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
        $campaign = Campaign::where('user_id',$id)->get()->count();
        $contact = Customer::where('user_id',$id)->get()->count();
        $phone_number = PhoneNumber::where('user_id',$id)->first();
        $server = Config::where('config_name','status_server')->first();
        
        if(!is_null($server))
        {
          if($server->value == 'active')
          {
             $server_status = '<span class="span-connected">'.$server->value.'</span>'; 
          }
          else
          {
             $server_status = '<span class="down">'.$server->value.'</span>';
          }
        }
        else
        {
          $server_status = '-';
        } 

        if(!is_null($phone_number))
        {
          $phone_id = $phone_number->id;
          if($phone_number->status == 2)
          {
            $phone_status = '<span class="span-connected">Connected</span>';
          }
          else
          {
            $phone_status = '<span class="down">Disconnected</span>';
          }
          
        }
        else
        {
          $phone_status = '-';
        }
        

        $latest_list = DB::select('select * from lists where user_id ='.$id.' and DATE(created_at) > (NOW() - INTERVAL 7 DAY)');
        (count($latest_list) > 0)? $latest = '+'.count($latest_list) : $latest = count($latest_list);

        $reminder = ReminderCustomers::where([['user_id','=',$id],['status','=',0]])->get()->count();

        $reminder_sent = ReminderCustomers::where([['user_id','=',$id],['status','>',0]])->get()->count();

        $broadcast = BroadCast::where([['broad_casts.user_id','=',$id],['broad_cast_customers.status','=',0]])
            ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')->get()->count();

        $broadcast_sent = BroadCast::where([['broad_casts.user_id','=',$id],['broad_cast_customers.status','>',0]])
            ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')->get()->count();

        $total_message = $reminder + $broadcast;
        $total_sending_message = $reminder_sent + $broadcast_sent;

        $users = User::find($id);
        $expired = Carbon::now()->addDays($users->day_left)->toDateString();
        $phone = PhoneNumber::where('user_id',$id)->first();

        if(is_null($phone))
        {
            $max_counter = 0;
        }
        else
        {
            $max_counter = number_format($phone->max_counter);
        }

        // STATISTIC

        //LIST
        $graphlist = Customer::where('user_id',$id)->select(DB::raw('COUNT(DATE_FORMAT(created_at,"%Y-%m-%d")) AS total_contacts,DATE_FORMAT(created_at,"%Y-%m-%d") AS join_date'))->groupBy('join_date')->orderBy('join_date','asc');

        $total_graphic = $graphlist->get()->count() - 30;

        if($total_graphic < 30)
        {
          $graph_contacts = $graphlist->get();
        }
        else
        {
          $graph_contacts = $graphlist->skip($total_graphic)->take(30)->get();
        }

        $graph_list = array();
        if($graph_contacts->count() > 0)
        {
          foreach($graph_contacts as $row)
          {
            $graph_list[$row->join_date] = $row->total_contacts;
          }
        }

        //MESSAGE
        $graph_broadcast_message = BroadCastCustomers::where([['broad_casts.user_id',$id],['broad_cast_customers.status','>',0]])
          ->join('broad_casts','broad_casts.id','=','broad_cast_customers.broadcast_id')
          ->select(DB::raw('COUNT(DATE_FORMAT(broad_cast_customers.updated_at,"%Y-%m-%d")) AS total_messages,DATE_FORMAT(broad_cast_customers.updated_at,"%Y-%m-%d") AS date_send'))->groupBy('date_send')->orderBy('date_send','asc');

        $graph_message = ReminderCustomers::where([['user_id',$id],['status','>',0]])->select(DB::raw('COUNT(DATE_FORMAT(updated_at,"%Y-%m-%d")) AS total_messages,DATE_FORMAT(updated_at,"%Y-%m-%d") AS date_send'))->union($graph_broadcast_message)->groupBy('date_send')->orderBy('date_send','asc');

        // dd($graph_message->get());

        $total_send_message = $graph_message->get()->count() - 30;

        if($total_send_message < 30)
        {
          $graph_send = $graph_message->get();
        }
        else
        {
          $graph_send = $graph_message->skip($total_send_message)->take(30)->get();
        }

        $graph_send_message = array();
        if($graph_send->count() > 0)
        {
          foreach($graph_send as $row)
          {
            $graph_send_message[$row->date_send] = $row->total_messages;
          }
        }

        $data = array(
          'lists'=>$lists,
          'latest_lists'=>$latest,
          'campaign'=>$campaign,
          'contact'=>$contact,
          'total_message'=>$total_message,
          'total_sending_message'=>$total_sending_message,
          'membership'=>$users->membership,
          'expired'=>Date("d M Y",strtotime($expired)),
          'status'=>$users->status,
          'quota'=>$max_counter,
          'phone_status'=>$phone_status,
          'server_status'=>$server_status,
          'graph_contacts'=>$graph_list,
          'graph_messages'=>$graph_send_message
        );

        return view('home',$data);
    }

    public function jsonEncode(Request $req)
    {
      return json_encode($req->data);
    }

    public function google_form()
    {
      $user = Auth::user();
      $key = "";
      $phoneNumber = PhoneNumber::
                      where("user_id",$user->id)
                      ->first();
      if (!is_null($phoneNumber)) {
        $key = $phoneNumber->filename;
      }      
      return view('google-form',["key"=>$key]);
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
      $lists = UserList::where('user_id',$userid)->get();
      $user = User::find($userid);
      $user_status = $user->status;
      
      if($user_status < 1 && $lists->count() < 1)
      {
        $data['status'] = 'buy';
      }
      elseif($user_status < 1 && $lists->count() > 0) 
      {
        $data['status'] = 'exp';
      } 
      elseif($phone->count() < 1) 
      {
        $data['status'] = 'phone';
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
