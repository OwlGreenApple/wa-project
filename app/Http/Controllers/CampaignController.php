<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\BroadCastController;
use App\UserList;
use App\Campaign;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use App\Rules\CheckDateEvent;
use App\Rules\CheckValidListID;
use App\Rules\CheckEventEligibleDate;
use App\Rules\CheckBroadcastDate;
use App\Rules\CheckExistIdOnDB;
use App\Rules\EligibleTime;
use DB;
use Carbon\Carbon;

class CampaignController extends Controller
{
    public function index()
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data['lists'] = $lists;
      return view('campaign.campaign',$data);
    }

    public function CreateCampaign() 
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data = array(
          'lists'=>$lists,
      );

      return view('campaign.create-campaign',$data);
    }

    public function SaveCampaign(Request $request)
    {
      $campaign = $request->campaign_type;
      if($request->schedule == 0)
      {
          $request->day = 0;
      }

      if($campaign == 'event')
      {
        $rules = array(
            'campaign_name'=>['required','max:50'],
            'list_id'=>['required',new CheckValidListID],
            'event_time'=>['required',new CheckDateEvent,new CheckEventEligibleDate($request->day)],
            'hour'=>['required','date_format:H:i',new EligibleTime($request->event_time)],
            'message'=>['required','max:4095']
        );

        if($request->schedule > 0){
          $rules['day'] = ['required','numeric','min:-90','max:100'];
        }

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails()){
            $error = array(
              'err'=>'ev_err',
              'campaign_name'=>$err->first('campaign_name'),
              'list_id'=>$err->first('list_id'),
              'event_time'=>$err->first('event_time'),
              'day'=>$err->first('day'),
              'hour'=>$err->first('hour'),
              'msg'=>$err->first('message'),
            );
            return response()->json($error);
        }

        $event = new EventController;
        $saveEvent = $event->saveEvent($request);

        if(!empty($saveEvent))
        {
            $data['err'] = 0;
            $data['message'] = $saveEvent;
            return response()->json($data);
        }
      } 
      
      if($campaign == 'auto')
      {   
        /* Validator */
        $rules = array(
            'campaign_name'=>['required','max:50'],
            'list_id'=>['required',new CheckValidListID],
            'day'=>['required','numeric','min:1','max:100'],
            'hour'=>['required','date_format:H:i'],
            'message'=>['required','max:4095']
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        if($validator->fails()){
            $error = array(
              'err'=>'responder_err',
              'campaign_name'=>$err->first('campaign_name'),
              'list_id'=>$err->first('list_id'),
              'day'=>$err->first('day'),
              'hour'=>$err->first('hour'),
              'msg'=>$err->first('message'),
            );
            return response()->json($error);
        }

        $auto = new ReminderController;
        $saveAutoReponder = $auto->saveAutoReponder($request);
        
        if(!empty($saveAutoReponder))
        {
            $data['err'] = 0;
            $data['message'] = $saveAutoReponder;
            return response()->json($data);
        }
      }
      else
      {
        /* Validator */
        $rules = array(
          'campaign_name'=>['required','max:50'],
          'list_id'=>['required', new CheckValidListID],
          'date_send'=>['required',new CheckBroadcastDate],
          'hour'=>['required','date_format:H:i',new EligibleTime($request->date_send)],
          'message'=>['required','max:4095'],
        );

        $validator = Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            $error = $validator->errors();
            $data_error = [
              'err'=>'broadcast_err',
              'campaign_name'=>$error->first('campaign_name'),
              'list_id' =>$error->first('list_id'),
              'group_name' =>$error->first('group_name'),
              'channel_name' =>$error->first('channel_name'),       
              'date_send'=>$error->first('date_send'),
              'hour'=>$error->first('hour'),
              'msg'=>$error->first('message'),
            ];

            return response()->json($data_error);
        }

        $broadcast = new BroadCastController;
        $saveBroadcast = $broadcast->saveBroadCast($request);

        if(!empty($saveBroadcast))
        {
            $data['message'] = $saveBroadcast;
            return response()->json($data);
        }
      }
    }

    public function addMessageAutoResponder($campaign_id)
    {
      $user_id = Auth::id();
      $campaign = Campaign::find($campaign_id);
      if ($campaign->user_id<>$user_id){
        return "Not Authorized";
      }
      $lists = UserList::where('user_id',$user_id)->get();
      $current_list = UserList::where('id',$campaign->list_id)->select('label')->first();
      $data['lists'] = $lists;
      $data['campaign_id'] = $campaign_id;
      $data['campaign_name'] = $campaign->name;
      $data['currentlist'] = $current_list->label;
      $data['currentlistid'] = $campaign->list_id;
      return view('reminder.add-message-auto-responder',$data);
    }

    public function addMessageEvent($campaign_id)
    {
      $user_id = Auth::id();
      $campaign = Campaign::find($campaign_id);
      if ($campaign->user_id<>$user_id){
        return "Not Authorized";
      }
      $lists = UserList::where('user_id',$user_id)->get();
      $current_list = UserList::where('id',$campaign->list_id)->select('label')->first();
      $data['lists'] = $lists;
      $data['campaign_id'] = $campaign_id;
      $data['campaign_name'] = $campaign->name;
      $data['currentlist'] = $current_list->label;
      $data['currentlistid'] = $campaign->list_id;
      return view('event.add-message-event',$data);
    }

    public function searchCampaign(Request $request)
    {
        $userid = Auth::id();
        $search = $request->search;
        $type = $request->type;
        $data = array();

        if($search == null && $type == null)
        {
          $campaign = Campaign::where([['campaigns.user_id',$userid],['campaigns.type','<',3]])
                      ->leftJoin('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->get();
        }
        elseif($type <> null)
        { 
          $campaign = Campaign::where([['campaigns.user_id',$userid],['campaigns.type','=',$type]])
                      ->leftJoin('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->get();
        }
        else
        {
          $campaign = Campaign::where([['name','like','%'.$search.'%'],['userid',$userid],['campaigns.type','<',3]]) 
                      ->leftJoin('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->get();
        }

        if($campaign->count() > 0)
        {
            foreach($campaign as $row)
            {
                if($row->type == 2) 
                {
                  $broadcast = BroadCast::where('campaign_id',$row->id)->first();
                  $list_id = $row->list_id;
                  $lists = UserList::find($list_id);
                  
                  if(!is_null($lists))
                  {
                      $label = $lists->label;
                  }
                  else 
                  {
                      $label = null;
                  }

                  $broadcast_customer = BroadCastCustomers::where('broadcast_id','=',$broadcast->id)
                ->select(DB::raw('COUNT("id") AS total_message'))->first();

                  $broadcast_customer_open = BroadCastCustomers::where([['broadcast_id','=',$broadcast->id],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

                  $data[] = array(
                      'type'=>2,
                      'id'=>$broadcast->id,
                      'campaign_id'=>$row->id,
                      'campaign' => $row->name,
                      'group_name' => $broadcast->group_name,
                      'channel' => $broadcast->channel,
                      'date_send' => $broadcast->day_send,
                      'day_send' => Date('M d, Y',strtotime($broadcast->day_send)),
                      'sending' => Date('H:i',strtotime($broadcast->hour_time)),
                      'label' => $label,
                      'created_at' => Date('M d, Y',strtotime($row->created_at)),
                      'total_message' => $broadcast_customer->total_message,
                      'sent_message' => $broadcast_customer_open->total_sending_message,
                      'messages' => $broadcast->message,
                  );
                }
                else //REMINDER
                {
                    if($row->type == 0)
                    {
                        $reminder = Reminder::where([['campaign_id',$row->id],['is_event',1],['tmp_appt_id','=',0]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();
                    }
                    else {
                        $reminder = Reminder::where([['campaign_id',$row->id],['is_event',0],['tmp_appt_id','=',0]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();
                    } 

                    if(!is_null($reminder))
                    {
                        $days = (int)$reminder->days;

                        $reminder_customer = ReminderCustomers::where('reminder_id','=',$reminder->id)->select(DB::raw('COUNT("id") AS total_message'))->first();

                        $reminder_customer_open = ReminderCustomers::where([['reminder_id','=',$reminder->id],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

                        if($row->type == 0)
                        {
                          // EVENT
                          if($days < 0){
                            $abs = abs($days);
                              $event_time = Carbon::parse($reminder->event_time)->subDays($abs);
                            }
                          else
                          {
                              $event_time = Carbon::parse($reminder->event_time)->addDays($days);
                          }

                          $data[] = array(
                            'type'=>0,
                            'id'=>$row->id,
                            'campaign_name'=>$row->name,
                            'sending'=>Date('M d, Y',strtotime($event_time)),
                            'sending_time' => Date('H:i',strtotime($reminder->hour_time)),
                            'label'=>$row->label,
                            'created_at'=>Date('M d, Y',strtotime($row->created_at)),
                            'total_message' => $reminder_customer->total_message,
                            'sent_message' => $reminder_customer_open->total_sending_message
                          );
                        }
                        else
                        {
                          // AUTORESPONDER
                          if($days > 1)
                          {
                              $message = 'Days from after Subscribed';
                          }
                          else{
                              $message = 'Day from after Subscribed';
                          }
                          $data[] = array(
                            'type'=>1,
                            'id'=>$row->id,
                            'campaign_name' => $row->name,
                            'sending' => $days.' '.$message,
                            'sending_time' => Date('H:i',strtotime($reminder->hour_time)),
                            'label' => $row->label,
                            'created_at' => Date('M d, Y',strtotime($row->created_at)),
                            'total_message' => $reminder_customer->total_message,
                            'sent_message' => $reminder_customer_open->total_sending_message,
                          );
                        }
                    }
                    else
                    {
                      // IF REMINDER IS EMPTY
                      ($row->type == 0)?$type = 0 : $type = 1;
                      
                      $data[] = array(
                        'type'=>$type,
                        'id'=>$row->id,
                        'campaign_name' => $row->name,
                        'sending' => '-',
                        'label' => $row->label,
                        'created_at' => Date('M d, Y',strtotime($row->created_at)),
                        'total_message' => 0,
                        'sent_message' => 0,
                      );
                    }

                } //END IF CAMPAIGN

            }//ENDFOREACH
        }

        return view('campaign.campaign-search',['data'=>$data]);
    }

    public function listCampaign($campaign_id,$is_event,$active)
    {
        /*
          1 = Active
          0 = inactive
        */

        $userid = Auth::id();
        ($is_event == 0 || $is_event == 1 || $is_event == 'broadcast')?$invalid = false : $invalid = true;

        if($invalid == true)
        {
           return redirect('create-campaign');
        }

        ($active == 0 || $active == 1)?$invalid = false : $invalid = true;

        if($invalid == true)
        {
           return redirect('create-campaign');
        }

        if(empty($campaign_id) || $campaign_id==null)
        {
            return redirect('create-campaign');
        }

        $checkid = Campaign::where([['id',$campaign_id],['user_id',$userid]])->first();

        if(is_null($checkid))
        {
            return redirect('create-campaign');
        }

        if($invalid == false)
        {
          if($active == 1)
          {
            $active = true;
            $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','=',0]])
            ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminders.id AS rid')
            // ->distinct()
            ->get();
          }
          else
          {
            $active = false;
            $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','>',0]])
            ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.status')
            ->get();
          }
        }

        return view('campaign.list_campaign',['campaign_id'=>$campaign_id,'campaign_name'=>$checkid->name,'active'=>$active,'campaigns'=>$campaigns,'is_event'=>$is_event]);
    }

    public function delCampaign(Request $request)
    {
        $user_id = Auth::id();
        $campaign = Campaign::find($request->id);

        try {
          $campaign->delete();
          if($request->mode == "broadcast") {
            $broadCasts = BroadCast::where([['campaign_id',$campaign->id],['user_id',$user_id]])->get();
            foreach($broadCasts as $broadCast) {
              $broadcastcustomer = BroadCastCustomers::where('broadcast_id','=',$broadCast->id)->delete();
            }
            BroadCast::where([['campaign_id',$campaign->id],['user_id',$user_id]])->delete();
          }
          if( ($request->mode == "event") || ($request->mode == "auto_responder") ) {
            $reminders = Reminder::where([['campaign_id',$campaign->id],['user_id',$user_id]])->get();
            foreach($reminders as $reminder) {
              $remindercustomer = ReminderCustomers::where('reminder_id','=',$reminder->id)->delete();
            }
            Reminder::where([['campaign_id',$campaign->id],['user_id',$user_id]])->delete();
          }
        }
        catch(Exception $e)
        {
           return response()->json(['message'=>'Sorry, unable to delete , contact administrator']);
        }


        return response()->json(['message'=>'Your campaign has been deleted successfully']);
    }

    public function editCampaign(Request $request)
    {
        $userid = Auth::id();
        $campaign_name = $request->campaign_name;
        $campaign_id = $request->campaign_id;

        $cond = [
          ['id',$campaign_id],
          ['user_id',$userid],
        ];

        $rules = [
          'campaign_name'=>['required','min:4','max:50'],
          'campaign_id'=>['required',new CheckExistIdOnDB('campaigns',$cond)],
        ];

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails())
        {
            $error = $validator->errors();
            $data = array(
                'campaign_name'=>$error->first('campaign_name'),
                'campaign_id'=>$error->first('campaign_id'),
                'success'=>0,
            );
            return response()->json($data);
        }
        // END VALIDATOR 

        try {
          Campaign::where([['id',$campaign_id],['user_id',$userid]])->update(['name'=>$campaign_name]);
          $data = array(
            'success'=>1,
            'id'=>$campaign_id,
            'campaign_name'=>$campaign_name,
          );
        }
        catch(Exception $e)
        {
           $data = array(
            'success'=>0,
            'error_server'=>'Sorry, unable to update your campaign name, try again later',
          );
        }

        return response()->json($data);
    }


/* end controller */
}
