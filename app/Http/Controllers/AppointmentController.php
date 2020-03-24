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
        $listid = Campaign::find($campaign_id)->list_id;
        $save = false;
        $count = 0;

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

              $update_reminder = array(
                'days'=>$days,
                'hour_time'=>$request->hour,
                'message'=>$request->message,
              );

              Reminder::where([['tmp_appt_id',$request->is_update],['user_id',$userid],['campaign_id',$campaign_id]])->update($update_reminder);

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
          $tmp_appt_id = $template_appt->id;
          $save = true;
        }
        catch(Exception $e)
        {
          $data['success'] = 0;
          $data['message'] = 'Sorry, unable to create schedule template, please try again later';
          return response()->json($data);
        }

        if($save == true)
        {
            // TO OBTAIN CUSTOMER FROM PREVIOUS TEMPLATE WITH SAME CAMPAIGN
            $getcustomer = ReminderCustomers::where([['reminders.campaign_id',$campaign_id]])
            ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('customers.id','reminders.event_time')
            ->distinct()
            ->get();

            if($getcustomer->count() > 0)
            {
              foreach($getcustomer as $row)
              {
                $reminder = new Reminder;
                $reminder->user_id = $userid;
                $reminder->list_id = $listid;
                $reminder->campaign_id = $campaign_id;
                $reminder->tmp_appt_id = $tmp_appt_id;
                $reminder->is_event = 2;   
                $reminder->days = $days;
                $reminder->hour_time = $request->hour;
                $reminder->event_time = $row->event_time;
                $reminder->message = $request->message;

                try {
                  $reminder->save();
                  $reminderid[] = $reminder->id;
                  $customerid[] = $row->id;
                }
                catch(Exception $e)
                {
                  $data['success'] = 0;
                  $data['message'] = 'Sorry, unable to create schedule template, please try again later';
                  return response()->json($data);
                }
              }
            }

            $newcustomer = array_combine($reminderid,$customerid);
        }

        if(count($newcustomer) > 0)
        {
          foreach($newcustomer as $reminder_id=>$customer_id)
          {
            $reminder_customer = new ReminderCustomers;
            $reminder_customer->user_id = $userid;
            $reminder_customer->list_id = $listid;
            $reminder_customer->reminder_id = $reminder_id;
            $reminder_customer->customer_id = $customer_id;

            try {
              $reminder_customer->save();
              $count++;
            }
            catch(Exception $e)
            {
              $data['success'] = 0;
              $data['message'] = 'Sorry, unable to create schedule template, please try again later';
              return response()->json($data);
            }
          }
        }
        else {
          $data['success'] = 1;
          $data['message'] = 'Your appointment template has been created!';
          return response()->json($data);
        }

        //use count to make sure data inserted on for loop
        if($count > 0)
        {
          $data['success'] = 1;
          $data['message'] = 'Your appointment template has been created!';
          return response()->json($data);
        }
        
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

    public function formAppointment($campaign_id)
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

        return view('appointment.form_apt',['id'=>$campaign_id,'list_id'=>$checkid->list_id]);
    }

    public function displayCustomerPhone(Request $request)
    {
        $phone = $request->phone;
        $list_id = $request->list_id;
        $userid = Auth::id();

        $list = UserList::find($list_id);
        $customer = Customer::where([['user_id',$userid],['list_id','=',$list_id],['telegram_number','=',$phone]])->get();

        return view('appointment.customer_apt',['customer'=>$customer,'url'=>$list->name]);
    }

    public function saveAppointmentTime(Request $request)
    {
        $user_id = Auth::id();
        $campaign_id = $request->campaign_id;
        $list_id = $request->list_id;
        $customer_id = $request->customer_id;
        $date_send = $request->date_send;
        $count = 0;
        $reminderid = array();
        $appointments = TemplateAppointments::where([['campaign_id',$campaign_id],['user_id',$user_id]])->get();

        if($appointments->count() > 0)
        {
            foreach($appointments as $row)
            {
              $reminder = new Reminder;
              $reminder->user_id = $user_id;
              $reminder->list_id = $list_id;
              $reminder->campaign_id = $campaign_id;
              $reminder->tmp_appt_id = $row->id;
              $reminder->is_event = 2;
              $reminder->days = $row->days;
              $reminder->hour_time = $row->time_sending;
              $reminder->event_time = $date_send;
              $reminder->message = $row->message;

              try {
                $reminder->save();
                $reminderid[] = $reminder->id;
              }
              catch(Exception $e)
              {
                $result = array(
                    'success'=>0,
                    'message'=>'Sorry unable to create appointment, please try again later',
                );
                return response()->json($result);
              }
            }
        }

        if(count($reminderid) > 0)
        {
           foreach($reminderid as $id)
           {
              $reminder_customer = new ReminderCustomers;
              $reminder_customer->user_id = $user_id;
              $reminder_customer->list_id = $list_id;
              $reminder_customer->reminder_id = $id;
              $reminder_customer->customer_id = $customer_id;

              try {
                $reminder_customer->save();
                $count++;
              }
              catch(Exception $e)
              {
                $result = array(
                    'success'=>0,
                    'message'=>'Sorry unable to create appointment, please try again later',
                );
                return response()->json($result);
              }
           }
        }
        else
        {
            $result = array(
                'success'=>0,
                'message'=>'Sorry unable to create appointment, please try again later',
            );
            return response()->json($result);
        }

        if($count > 0)
        {
            $result = array(
                'success'=>1,
                'message'=>'Your appointment has been created!',
            );
            return response()->json($result);
        }

    }

/* end of class */
}
