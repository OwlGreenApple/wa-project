<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\User;
use App\AdminSetting;
use App\Countries;

class AdminController extends Controller
{

    public function InsertCountry()
    {
        return view('admin.insert_country');
    }

    public function saveCountry(Request $request)
    {
        $country_name = $request->country_name;
        $code_country = str_replace("+","",$request->code_country);
        $update = $request->update;

        if($update == null)
        {
          $country = new Countries;
          $country->name = $country_name;
          $country->code = $code_country;

          try {
            $country->save();
            $data['msg'] = 'Country has been saved';
          }
          catch(Exception $e)
          {
            $data['msg'] = 'Sorry, unable to save country';
          }
          return response()->json($data);
        }
        else 
        {
          $col = array(
            'name'=>$country_name,
            'code'=>$code_country,
          );

          try {
              Countries::where('id',$update)->update($col);
              $data['msg'] = 'Country has been edited';
          }
          catch(Exception $e)
          {
            $data['msg'] = 'Sorry, unable to update country';
          }
          return response()->json($data);
        }
    }

    public function showCountry()
    {
        $countries = Countries::all();
        return view('admin.country_table',['country'=>$countries]);
    }

    public function delCountry(Request $request)
    {
        $idcountry = $request->id;

        try{
          Countries::where('id',$idcountry)->delete();
          $data['msg'] = 'Country deleted successfully';
        }
        catch(Exception $e)
        {
          $data['msg'] = 'Country deleted successfully';
        }
        return response()->json($data);
    }

    /*********** OLD CODES ***********/

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

    #TO CONTROL RATE OF SENDING MESSAGE
    public function SendingRate()
    {
      $check = AdminSetting::where('id','=',1)->first();
      $data = array(
          'sending_start'=>1,
          'sending_end'=>1,
          'delay_start'=>1,
          'delay_end'=>1,
      );

      if(!is_null($check))
      {
        $data = array(
          'sending_start'=>$check->total_message_start,
          'sending_end'=>$check->total_message_end,
          'delay_start'=>$check->delay_message_start,
          'delay_end'=>$check->delay_message_end,
        );
      }
     
      return view('admin.sendingrate',['data'=>$data]);
    }

    public function SaveSettings(Request $request)
    {
        $id_admin = Auth::id();
        $adminsetting = null;
        $check = AdminSetting::where('id','=',1)->first();

        if(is_null($check))
        {
          $adminsetting = new AdminSetting;
          $adminsetting->total_message_start = $request->total_sending_start;
          $adminsetting->total_message_end = $request->total_sending_end;
          $adminsetting->delay_message_start = $request->delay_sending_start;
          $adminsetting->delay_message_end = $request->delay_sending_end;
          $adminsetting->id_admin = $id_admin;
          $adminsetting->save();

          if($adminsetting <> null)
          {
            return redirect('sendingrate')->with('status','Admin settings saved successfully');
          } else {
            return redirect('sendingrate')->with('error','Admin settings failed to save');
          }
        }
        else {
            $data_update = array(
              'total_message_start'=>$request->total_sending_start,
              'total_message_end'=>$request->total_sending_end,
              'delay_message_start'=>$request->delay_sending_start,
              'delay_message_end'=>$request->delay_sending_end,
              'id_admin'=>$id_admin
            );
            $update = AdminSetting::where('id','=',1)->update($data_update);
        }   

        if($update == true)
        {
          return redirect('sendingrate')->with('status','Admin settings updated successfully');
        }
        else
        {
          return redirect('sendingrate')->with('error','Admin settings failed to update');
        }
    }

     public function SaveSettingsDelaySending(Request $request)
    {
        $id_admin = Auth::id();
        $check = AdminSetting::where('id','=',1)->first();

        if(isset($request->total_sending_start) && isset($request->$total_sending_end))
        {
            $start = $request->total_sending_start;
            $end = $request->total_sending_end;
            $update = AdminSetting::update(['total_message_start'=>$start,'total_message_end'=>$end,'is_admin'=>$id_admin]);
        }
        else {
            $start = $request->delay_sending_start;
            $end = $request->delay_sending_end;
            $update = AdminSetting::update(['delay_message_start'=>$start,'delay_message_end'=>$end,'is_admin'=>$id_admin]);
        }

        if(is_null($check))
        {
          $adminsetting = new AdminSetting;
          $adminsetting->total_message_start = $request->total_sending_start;
          $adminsetting->total_message_end = $request->total_sending_end;
          $adminsetting->delay_message_start = $request->total_sending_start;
        }    

        if($update == true)
        {
          return redirect('sendingrate')->with('status','<div class="alert alert-success" role="alert">Admin settings updated successfully</div>');
        }
        else
        {
          return redirect('sendingrate')->with('status','<div class="alert alert-danger" role="alert">Admin settings failed to update</div>');
        }
    }

/* end class admincontroller */    
}
