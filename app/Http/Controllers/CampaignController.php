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
use DB;

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

      /*if($campaign == 'event')
      {
        $rules = array(
            'campaign_name'=>['required','max:50'],
            'list_id'=>['required',new CheckValidListID],
            'event_time'=>['required',new CheckDateEvent,new CheckEventEligibleDate($request->day)],
            'hour'=>['required','date_format:H:i'],
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
      */
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
          'hour'=>['required','date_format:H:i'],
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
      $data['lists'] = $lists;
      $data['campaign_id'] = $campaign_id;
      $data['campaign_name'] = $campaign->name;
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
      $data['lists'] = $lists;
      $data['campaign_id'] = $campaign_id;
      $data['campaign_name'] = $campaign->name;
      return view('event.add-message-event',$data);
    }

    public function searchCampaign(Request $request)
    {
        $search = $request->search;
        $data['event'] = $data['reminder'] = $data['broadcast'] = array();
        $campaign = Campaign::where('name','like','%'.$search.'%')->get();

        if($campaign->count() > 0)
        {
            foreach($campaign as $row)
            {
                if($row->type == 0){
                  $lists = UserList::where([['id',$row->list_id]])->first();

                  $event = Reminder::where('campaign_id',$row->id)->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();

                  $reminder_customer = ReminderCustomers::where('reminder_id','=',$event->id)->select(DB::raw('COUNT("id") AS total_message'))->first();

                  $reminder_customer_open = ReminderCustomers::where([['reminder_id','=',$event->id],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

                  $data['event'][] = array(
                      'id'=>$row->id,
                      'campaign_name'=>$row->name,
                      'sending'=>Date('M d, Y',strtotime($event->event_time)),
                      'label'=>$lists->label,
                      'created_at'=>Date('M d, Y',strtotime($row->created_at)),
                      'total_message' => $reminder_customer->total_message,
                      'sent_message' => $reminder_customer_open->total_sending_message,
                  );
                }
                else if($row->type == 1) {
                  $lists = UserList::where([['id',$row->list_id]])->first();

                  $sending = $row->days;
                  if($sending > 1)
                  {
                      $message = 'days from subscriber join on your list';
                  }
                  else{
                      $message = 'day from subscriber join on your list';
                  }

                  $reminder = Reminder::where('campaign_id',$row->id)->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();

                  $reminder_customer = ReminderCustomers::where('reminder_id','=',$reminder->id)->select(DB::raw('COUNT("id") AS total_message'))->first();

                  $reminder_customer_open = ReminderCustomers::where([['reminder_id','=',$reminder->id],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

                  $data['reminder'][] = array(
                      'id'=>$reminder->id,
                      'campaign_name'=>$row->name,
                      'sending'=>$sending.' '.$message,
                      'label'=>$lists->label,
                      'created_at'=>Date('M d, Y',strtotime($row->created_at)),
                      'total_message' => $reminder_customer->total_message,
                      'sent_message' => $reminder_customer_open->total_sending_message,
                  );
                }
                else if($row->type == 2) {
                  $broadcast = BroadCast::where('campaign_id',$row->id)->first();

                  $lists = UserList::where([['id',$broadcast->list_id]])->first();

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

                  $data['broadcast'][] = array(
                      'id'=>$broadcast->id,
                      'campaign' => $row->name,
                      'group_name' => $broadcast->group_name,
                      'channel' => $broadcast->channel,
                      'day_send' => Date('M d, Y',strtotime($broadcast->day_send)),
                      'sending' => Date('h:i',strtotime($broadcast->hour_time)),
                      'label' => $label,
                      'created_at' => Date('M d, Y',strtotime($row->created_at)),
                      'total_message' => $broadcast_customer->total_message,
                      'sent_message' => $broadcast_customer_open->total_sending_message,
                  );
                }
            }
        }

        return view('campaign.campaign-search',['data'=>$data]);
    }

    public function reportReminder()
    {
      return view('campaign.report-reminder');
    }


/* end controller */
}
