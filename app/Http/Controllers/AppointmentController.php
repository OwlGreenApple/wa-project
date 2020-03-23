<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\UserList;
use App\Customer;
use App\User;
use App\Campaign;
use App\Reminder;
use App\ReminderCustomers;
use App\TemplateAppointments;

class AppointmentController extends Controller
{
    function index()
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data['lists'] = $lists;
      return view('appointment.index',$data);
    }

    public function tableAppointment(Request $request)
    {
        $userid = Auth::id();
        $data = array();
        $campaigns = Campaign::where([['campaigns.user_id',$userid],['campaigns.type',3]])
                    ->join('lists','lists.id','=','campaigns.list_id')
                    ->select('campaigns.*','lists.name AS url','lists.label')
                    ->get();

        if($campaigns->count() > 0)
        {
            foreach($campaigns as $row) {
              $contacts = Customer::where([['user_id',$userid],['list_id',$row->list_id]])->get()->count();
              $data[] = array(
                'campaign_id'=>$row->id,
                'name'=>$row->name,
                'url'=>$row->url,
                'label'=>$row->label,
                'created_at'=>Date('d-M-Y',strtotime($row->created_at)),
                'contacts'=>$contacts,
              );
            }
        }

        return view('appointment.table_apt',['data'=>$data]);

        /*if($request->search == null)
        {

        }*/
    }

    public function listAppointment($campaign_id)
    {
        $userid = Auth::id();
        if(empty($campaign_id) || $campaign_id==null)
        {
            return redirect('create-apt');
        }

        $checkid = Campaign::where([['id',$campaign_id],['user_id',$userid]])->first();

        if(is_null($checkid))
        {
            return redirect('create-apt');
        }

        return view('appointment.list_apt',['campaign_id'=>$campaign_id]);
    }

    public function listTableAppointments(Request $request)
    {
        $userid = Auth::id();
        $campaign_id = $request->campaign_id;
        $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',2],['reminders.user_id',$userid]])
          ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.event_time','customers.name','customers.telegram_number')
          ->paginate(5);

        return view('appointment.list_table_apt',['campaigns'=>$campaigns]);
    }

    function createAppointment()
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data['lists'] = $lists;
      return view('appointment.create_apt',['lists'=>$lists]);
    }

    public function saveAppointment(Request $request)
    {
      $userid = Auth::id();
      $appt = new Campaign;
      $appt->name = $request->name_app;
      $appt->type = 3;
      $appt->list_id = $request->list_id;
      $appt->user_id = $userid;

      try {
        $appt->save();
        $data['id'] = $appt->id;
        $data['success'] = 1;
        $data['message'] = 'Your appointment has been created';
      }
      catch(Exception $e)
      {
        $data['success'] = 0;
        $data['message'] = 'Error, Sorry Your appointment fail to create, try again later';
      }
      return response()->json($data);
    }

    public function editAppointment($id)
    {
        $userid = Auth::id();
        if(empty($id) || $id==null)
        {
            return redirect('create-apt');
        }

        $checkid = Campaign::where([['id',$id],['user_id',$userid]])->first();

        if(is_null($checkid))
        {
            return redirect('create-apt');
        }

        return view('appointment.edit_apt',['id'=>$id]);
    }

    public function displayTemplateAppointment(Request $request)
    {
        $userid = Auth::id();
        $campaign_id = $request->id;
        $reminder = TemplateAppointments::where([['user_id',$userid],['campaign_id',$campaign_id]])->get();

        return view('appointment.display_apt_reminder',['reminder'=>$reminder]);
    }

    public function saveTemplateAppointment(Request $request)
    {
        $userid = Auth::id();
        $campaign_id = $request->campaign_id;

        if(isset($_POST['day']))
        {
            $days = $request->day;
        }
        else 
        {
            $days = 0;
        }

        //UPDATE CASE
        if($request->is_update <> null)
        {
            try 
            {
              $update = array(
                'days'=>$days,
                'time_sending'=>$request->hour,
                'message'=>$request->message,
              );

              TemplateAppointments::where([['user_id',$userid],['id',$request->is_update]])->update($update);
               $data['success'] = 1;
               $data['message'] = 'Your template appointment has been updated!';
               return response()->json($data);
            }
            catch(Exception $e)
            {
               $data['success'] = 0;
               $data['message'] = 'Sorry unable to update your template appointment, please try again later';
               return response()->json($data);
            }
        }

        //CREATE NEW CASE
        $template_appt = new TemplateAppointments;
        $template_appt->user_id = $userid;
        $template_appt->campaign_id = $campaign_id;
        $template_appt->days = $days;
        $template_appt->time_sending = $request->hour;
        $template_appt->message = $request->message;

        try {
          $template_appt->save();
          $data['success'] = 1;
          $data['message'] = 'Your schedule template has been created!';
        }
        catch(Exception $e)
        {
          $data['success'] = 0;
          $data['message'] = 'Sorry, unable to create schedule template, please try again later';
        }
        return response()->json($data);
    }

    public function editAppointmentTemplate(Request $request)
    {
        $userid = Auth::id();
        $template_id = $request->id;
        $template_appt = TemplateAppointments::where([['user_id',$userid],['id',$template_id]])->first();

        if(!is_null($template_appt))
        {
            $data = array(
              'day'=>$template_appt->days,
              'time_send'=>$template_appt->time_sending,
              'msg'=>$template_appt->message,
            );
            return response()->json($data);
        }
    }

    public function deleteAppointmentTemplate(Request $request)
    {
        $userid = Auth::id();
        $template_id = $request->id;

        try 
        {
          TemplateAppointments::where([['user_id',$userid],['id',$template_id]])->delete();
          $data['success'] = 1;
          $data['message'] = 'Your schedule template deleted successfully';
        }
        catch(Exception $e)
        {
          $data['success'] = 0;
          $data['message'] = 'Sorry, unable to delete your schedule template, please try again later';
        }
        return response()->json($data);
    }

    function formAppointment()
    {
        return view('appointment.form_apt');
    }

}
