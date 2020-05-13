<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Http\Controllers\CampaignController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Rules\CheckDateEvent;
use App\UserList;
use App\Reminder;
use App\ReminderCustomers;
use App\Templates;
use App\Customer;
use Carbon\Carbon;
use App\Campaign;
use App\Helpers\Alert;
use DB,Storage;

class EventController extends Controller
{

    public function index(Request $request){
      $userid = Auth::id();
      $data = array();
      $campaign = Campaign::where([['campaigns.user_id',$userid],['campaigns.type','=',0]])
                  ->join('lists','lists.id','=','campaigns.list_id')
                  ->orderBy('campaigns.id','desc')
                  ->select('campaigns.*','lists.label')
                  ->paginate(1);

      if($campaign->count() > 0)
      {
          foreach($campaign as $row)
          {
             $reminder = Reminder::where([['campaign_id',$row->id],['is_event',1],['tmp_appt_id','=',0]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();

             $campaigncontroller = new CampaignController;

              if(!is_null($reminder))
              {
                $total_message = $campaigncontroller->campaignsLogic($row->id,$userid,0,'=',0); 
                $total_delivered = $campaigncontroller->campaignsLogic($row->id,$userid,0,'>',0);

                $days = (int)$reminder->days;
                $total_template = Reminder::where('campaign_id',$row->id)->get()->count();

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
                  'total_template' => $total_template,
                  'total_message' => $total_message->count(),
                  'sent_message' => $total_delivered->count(),
                  'published'=>$row->status
                );
              }
          } // ENDFOREACH
      }

      if($request->ajax()) {
          return view('event.event',['data'=>$data,'paginate'=>$campaign]);
      }

      return view('event.index',['data'=>$data,'paginate'=>$campaign]);
    }

    public function createEvent(){
      $user_id = Auth::id();
      $data = array(
          'lists'=>displayListWithContact($user_id),
      );
      return view('event.event-form',$data);
    }

    public function saveEvent(Request $request)
    {
        $user = Auth::user();
				$folder="";
				$filename="";
				if($request->hasFile('imageWA')) {
					//save ke temp local dulu baru di kirim 
					$dt = Carbon::now();
          $ext = $request->file('imageWA')->getClientOriginalExtension();
					$folder = $user->id."/broadcast-image/";
					$filename = $dt->format('ymdHi').'.'.$ext;

          if(checkImageSize($request->file('imageWA')) == true || $imagewidth > 1280 || $imageheight > 1280)
          {
              $scale = scaleImageRatio($imagewidth,$imageheight);
              $imagewidth = $scale['width'];
              $imageheight = $scale['height'];
              resize_image($request->file('imageWA'),$imagewidth,$imageheight,false,$folder,$filename);
          }
          else
          {
              Storage::disk('s3')->put($folder.$filename,file_get_contents($request->file('imageWA')), 'public');
          }
				}
				
        if($request->schedule == 0){
            $request->day = 0;
        }

        $campaign_type = 0;

        if ($request->campaign_id=="new") {
          $campaign = new Campaign;
          $campaign->name =  $request->campaign_name;
          $campaign->type =  $campaign_type;
          $campaign->list_id = $request->list_id;
          $campaign->user_id = $user->id;
          $campaign->save();
          $campaign_id = $campaign->id;
        }
        else {
          $campaign_id = $request->campaign_id;
        }

        if ($request->reminder_id=="new") {
          $reminder = new Reminder;
        }
        else {
          $reminder = Reminder::find($request->reminder_id);
        }
        $reminder->user_id = $user->id;
        $reminder->list_id = $request->list_id;
        $reminder->campaign_id = $campaign_id;
        $reminder->is_event = 1;
        $reminder->days = $request->day;
        $reminder->hour_time = $request->hour;
        $reminder->event_time = $request->event_time;
        $reminder->image = $folder.$filename;
        $reminder->message = $request->message;
        $reminder->save();

        // if reminder stored / save successfully 
        if($reminder->save()){
            // retrieve customer id 
            $event = Reminder::where([
                    ['reminders.id','=',$reminder->id],
                    ['reminders.status','=',1],
                    ['reminders.is_event','=',1],
                    ['customers.status','=',1],
                    ['customers.list_id','=',$request->list_id],
                    ['customers.user_id','=',$user->id],
                    ])->join('customers','customers.list_id','=','reminders.list_id')->select('reminders.*','customers.id AS csid')->get();
        } else {
            return 'Error!! failed to set event';
        }

        // check whether user have customer 
        if($event->count() == 0){
            return 'Your event has been set!!';
        } 
        
        if($request->reminder_id=="new")
        {
            foreach($event as $col){
              $remindercustomer = new ReminderCustomers;
              $remindercustomer->user_id = $user->id;
              $remindercustomer->list_id = $col->list_id;
              $remindercustomer->reminder_id = $col->id;
              $remindercustomer->customer_id = $col->csid;
              $remindercustomer->save();
            }  
        }
        else
        {
            $data = 'Your event has been updated.';
            return $data;
        }

        // If successful insert data into event customer
        if($remindercustomer->save()){
            $data = 'Your event has been set!!';
        } else {
            $data = 'Error!! failed to set event for customer';
        }
        return $data;
    }

    public function displayEventList(Request $request)
    {
        $data = array();
        $id = Auth::id();
        $type = $request->type;

        if($type <> 0)
        {
            return 'Please do not modify default value';
        }
          $event = Campaign::where([['campaigns.user_id',$id],['campaigns.type',$type]])
            ->leftJoin('lists','lists.id','=','campaigns.list_id')
            ->select('campaigns.name','lists.label','campaigns.created_at','campaigns.id')
            ->orderBy('campaigns.id','desc')
            ->get();

          if($event->count() > 0)
          {
              foreach($event as $row)
              {
                  $reminder = Reminder::where([['campaign_id', $row->id],['is_event',1],['status',1]])->first();

                  if(!is_null($reminder))
                  {
                      $days = (int)$reminder->days;
                      if($days < 0){
                        $abs = abs($days);
                        $event_time = Carbon::parse($reminder->event_time)->subDays($abs);
                      }
                      else
                      {
                        $event_time = Carbon::parse($reminder->event_time)->addDays($days);
                      }

                      $reminder_customer = ReminderCustomers::where('reminder_id','=',$reminder->id)->select(DB::raw('COUNT("id") AS total_message'))->first();

                      $reminder_customer_open = ReminderCustomers::where([['reminder_id','=',$reminder->id_reminder],['status',1]])->select(DB::raw('COUNT("id") AS total_sending_message'))->first();

                       $data[] = array(
                        'id'=>$row->id,
                        'campaign_name' => $row->name,
                        'sending' => Date('M d, Y',strtotime($event_time)),
                        'label' => $row->label,
                        'created_at' => Date('M d, Y',strtotime($row->created_at)),
                        'total_message' => $reminder_customer->total_message,
                        'sent_message' => $reminder_customer_open->total_sending_message,
                      );
                  }
                  else
                  {
                      $data[] = array(
                        'id'=>$row->id,
                        'campaign_name' => $row->name,
                        'sending' => '-',
                        'label' => $row->label,
                        'created_at' => Date('M d, Y',strtotime($row->created_at)),
                        'total_message' => 0,
                        'sent_message' => 0,
                      );
                  }

              }//END FOREACH
          }
    
          return view('event.event-table',['event' => $data]);
    }

    public function delEvent(Request $request){
        $id = $request->id;
        $user_id = Auth::id();
        $event = Reminder::where([['user_id','=',$user_id],['id',$id]])->first();
        $campaign_id = $event->campaign_id;
        
        try {
          Reminder::where([['user_id','=',$user_id],['id',$id]])->delete();
          Campaign::where([['id',$campaign_id],['user_id',$user_id]])->delete();
          $success = true;
        }
        catch(Exception $e)
        {
          return response()->json(['message'=>'Sorry, unable to delete auto responder, contact administrator']);
        }

        if($success == true)
        {
          $remindercustomer = ReminderCustomers::where('reminder_id','=',$id)->get();
        }

        if($remindercustomer->count() > 0)
        {
             ReminderCustomers::where('reminder_id','=',$id)->delete();
        }
        return response()->json(['message'=>'Your event has been deleted successfully']);
    }

    public function duplicateEvent(Request $request)
    {
        $user_id = Auth::id();
        $campaign_id = $request->id;
        $campaign_name = $request->campaign_name;
        $event_date =  $request->event_time;
        $reminderid = array();

        $old_campaign = Campaign::find($campaign_id);

        if(!is_null($old_campaign))
        {
            $campaign = new Campaign;
            $campaign->name = $campaign_name;
            $campaign->type = 0;
            $campaign->list_id = $old_campaign->list_id;
            $campaign->user_id = $user_id;
            $campaign->status = 0;
            $campaign->save();
            $new_campaign_id = $campaign->id;
        }
        else
        {
            return response()->json(['message'=>'Invalid campaign']);
        }

        $row_event = Reminder::where([['campaign_id',$campaign_id],['user_id',$user_id],['is_event',1]])->get();

        if($row_event->count() > 0)
        {
            foreach($row_event as $row)
            {
              $list_id = $row->list_id;
              $event_day = $row->days;
              $event_sending = $row->hour_time;
              $event_message = $row->message;
              $event_image = $row->image;
              $oldreminderid[] = $row->id;

              $event = new Reminder;
              $event->user_id = $user_id;
              $event->list_id = $list_id;
              $event->campaign_id = $new_campaign_id;
              $event->is_event = 1;
              $event->days = $event_day;
              $event->event_time = $event_date;
              $event->hour_time = $event_sending;
              $event->image = $event_image;
              $event->message = $event_message;
              $event->save();
              $newreminderid[] = $event->id;
              $combine = array_combine($oldreminderid,$newreminderid);
            }
        }
        else 
        {
            return response()->json(['message'=>'Your campaign duplicated successfully']);
        }

        if(count($oldreminderid) > 0)
        { 
          $remindercustomer = ReminderCustomers::whereIn('reminder_id',$oldreminderid)->where('user_id',$user_id)->get();
        }
        else {
           return response()->json(['message'=>'Sorry, cannot duplicate your campaign, please call administrator']);
        }

        if($remindercustomer->count() > 0)
        {
           try
           {
              foreach($remindercustomer as $row)
              {
                $eventcustomer = new ReminderCustomers;
                $eventcustomer->user_id = $user_id;
                $eventcustomer->list_id = $list_id;
                $eventcustomer->reminder_id = $row->reminder_id;
                $eventcustomer->customer_id = $row->customer_id;
                $eventcustomer->save();
                $newremindercustomerid[] = $eventcustomer->id;
              }

              if(count($newremindercustomerid) > 0)
              {
                foreach($newremindercustomerid as $newid)
                {
                  foreach($combine as $oldreminderid=>$newreminderid)
                  {
                    ReminderCustomers::where([['id',$newid],['reminder_id',$oldreminderid]])->update(['reminder_id'=>$newreminderid]);
                  }
                }
              }

             return response()->json(['message'=>'Your campaign duplicated successfully']);
           }
           catch(Exception $e)
           {
             return response()->json(['message'=>'Sorry, cannot duplicate your campaign, please call administrator']);
           }
            
        }
        else 
        {
            return response()->json(['message'=>'Your campaign duplicated successfully']);
        }
    }

    /***************************************************************************** 
                                    OLD CODES
    /*****************************************************************************/

	PUBLIC FUNCTION JUSTCARBON() {
		//echo carbon::parse('2019-09-10 16:58:00')->subDays(5);
		echo carbon::parse('2019-09-10 16:58:00')->addDays(15);
	}

    public function eventAutoReply(){
        $id = Auth::id();
        $list = UserList::where([['user_id',$id],['is_event',1],['status',1]])->get();
        $templates = Templates::where('user_id',$id)->get();
        return view('event.event-autoreply',['data'=>$list, 'templates'=>$templates]);
    }

    /* perform autoreply */
    public function addEventAutoReply(Request $request){
		$user_id = Auth::id();
    	$req = $request->all();
    	$message = $req['message'];
        $list_id = $req['listid'];

        $checklist = Reminder::where([['list_id',$list_id],['hour_time','=',null]])->first();

        if(!is_null($checklist))
        {
            return redirect('eventautoreply')->with('error_autoreply','Sorry, you only allowed to create 1 auto reply per list');
        }

        $rules = array(
            'listid'=>['required'],
            'message'=>['required','max:3000'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('eventautoreply')->with('error',$err);
        } else {
 		    $eventautoreply = new Reminder;
            $eventautoreply->user_id = $user_id;
            $eventautoreply->list_id = $list_id;
            $eventautoreply->message = $message;
            $eventautoreply->save();
        }

        /* if reminder stored / save successfully */
        if($eventautoreply->save() == true){
        	return redirect('eventautoreply')->with('status','Your event has been set!!');
        } else {
        	return redirect('eventautoreply')->with('status_error','Error!! failed to set event auto reply');
        }
    } 

    public function addEvent(Request $request){
        $user_id = Auth::id();

        if($request->schedule == 0){
            $request->day = 0;
        }

        if($request->schedule > 0 &&  $request->day == 0){
           return redirect('eventform')->with('error_days','Please do not modify event days, if you want to use 0 days please use Hari H instead');
        }

        $rules = array(
            'list_id'=>['required'],
            'message'=>['required','max:3000'],
            'schedule'=>['required','numeric'],
            'day'=>['numeric'],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('eventform')->with('error',$err);
        } else {
            //$req['id'] == checkbox list
            $reminder = new Reminder;
            $reminder->user_id = $user_id;
            $reminder->list_id = $request->list_id;
            $reminder->days = $request->day;
            $reminder->hour_time = $request->hour;
            $reminder->message = $request->message;
            $reminder->save();
        }

        /* if reminder stored / save successfully */
        if($reminder->save() == true){
            /* retrieve customer id */
            $event = Reminder::where([
                    ['reminders.id','=',$reminder->id],
                    ['reminders.status','=',1],
                    ['customers.status','=',1],
                    ['customers.list_id','=',$request->list_id],
                    ])->join('customers','customers.list_id','=','reminders.list_id')->select('reminders.*','customers.id AS csid')->get();
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event');
        }

        /* check whether user have customer */
        if($event->count() == 0){
            return redirect('eventform')->with('status','Your event has been set!');
        } else {
             foreach($event as $col){
                $remindercustomer = new ReminderCustomers;
                $remindercustomer->user_id = $user_id;
                $remindercustomer->list_id = $col->list_id;
                $remindercustomer->reminder_id = $col->id;
                $remindercustomer->customer_id = $col->csid;
                $remindercustomer->save();
             }  
        }

        /* If successful insert data into event customer */
        if($remindercustomer->save() == true){
            return redirect('eventform')->with('status','Your event has been set!!');
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event for customer');
        }
    }

    public function addEventoldcode(Request $request){
        $user_id = Auth::id();
        $req = $request->all();
        $message = $req['message'];
        $event_date = $req['event_date'];
        $schedule = $req['schedule'];
        $sender = Sender::where('user_id',$user_id)->first();


        if((empty($req['day']) || empty($req['hour'])) && $schedule == 0)
        {
            $req['day'] = array(0);
            $req['hour'] = array($req['hour']);
            $days = $req['day'];
            $hour = $req['hour'];
        } else if((empty($req['day']) || empty($req['hour'])) && $schedule > 0) {
            $days = null;
            $hour = null;
        } else {
            $days = $req['day'];
            $hour = $req['hour'];
        }

        if($days == null || $hour == null) {
             return redirect('eventform')->with('errorday','Days and time should not blank');
        }

        $gettime = array_combine($days,$hour);

        $rules = array(
            'id'=>['required'],
            'message'=>['required','max:3000'],
            'event_date'=>['required',new CheckDateEvent],
        );

        $validator = Validator::make($request->all(),$rules);
        $err = $validator->errors();

        /* Validator */
        if($validator->fails()){
            return redirect('eventform')->with('error',$err);
        } else {
            /* prevent duplicate days */
            if($this->has_dupes($req['day']) == false){
                return redirect('eventform')->with('error_day','Do not use same value for day');
            } 
            //$req['id'] == checkbox list
            foreach($gettime as $day=>$hour){
                foreach($req['id'] as $row=>$list_id){
                    $reminder = new Reminder;
                    $reminder->user_id = $user_id;
                    $reminder->list_id = $list_id;
                    $reminder->event_date = $event_date;
                    $reminder->days = $day;
                    $reminder->hour_time = $hour;
                    $reminder->message = $message;
                    $reminder->save();
                }
            }
            $created_date = $reminder->created_at;
        }

        /* if reminder stored / save successfully */
        if($reminder->save() == true){
            /* retrieve customer id */
            foreach($req['id'] as $row=>$list_id){
                /* get customer data accoring on list */
                $event = Reminder::where([
                            ['reminders.event_date','=',$event_date],
                            ['reminders.list_id','=',$list_id],
                            ['reminders.status','=',1],
                            ['customers.status','=',1],
                            ['customers.list_id','=',$list_id],
                            ])->join('customers','customers.list_id','=','reminders.list_id')->select('reminders.*','customers.id AS csid')->get();

                foreach($event as $rows){
                    $eventcustomer[] = $rows;
                }

            }
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event');
        }

        /* check whether user have customer */
        if(count($eventcustomer) == 0){
            return redirect('eventform')->with('status','Your event has been set!');
        } else {
             foreach($eventcustomer as $col){
                $remindercustomer = new ReminderCustomers;
                $remindercustomer->user_id = $user_id;
                $remindercustomer->list_id = $col->list_id;
                $remindercustomer->sender_id = $sender->id;
                $remindercustomer->reminder_id = $col->id;
                $remindercustomer->customer_id = $col->csid;
                $remindercustomer->message = $message;
                $remindercustomer->save();
             }  
        }

        /* If successful insert data into event customer */
        if($remindercustomer->save() == true){
            return redirect('eventform')->with('status','Your event has been set!!');
        } else {
            return redirect('eventform')->with('status_error','Error!! failed to set event for customer');
        }
    }

    /* To check duplicate array */
    function has_dupes($array) {
    	foreach (array_count_values($array) as $value) {
    		 $value;
    	}
    	if($value > 1){
    		return false;
    	} else {
    		return true;
    	}
	}

    /* Disable or enable autoreply */
    public function turnEventAutoReply($id,$status)
    {

        if(empty($id))
        {
            return redirect('event');
        } 

        $eventautoreply = Reminder::where('id',$id)->update(['status'=>$status]);
        if($eventautoreply == true)
        {
            return redirect('event')->with('message','Event auto reply status has successully changed');
        } else {
            return redirect('event')->with('warning','Error! Unable to change event autoreply status');
        }
    }

    public function displayEventSchedule(Request $request)
    {
        $id = $request->id;
        $event = Reminder::where([['reminders.id',$id],['reminders.hour_time','<>',null],['lists.is_event','=',1]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.event_date','lists.id AS list_id')->first();

       $data = array(
            'date_event'=>$event->event_date,
            'day'=>$event->days,
            'hour'=>$event->hour_time,
            'list_id'=>$event->list_id,
       );

        return response()->json($data);
    }

    /* update event */

    public function updateEvent(Request $request)
    {
        $user_id = Auth::id();
        $sender = Sender::where('user_id',$user_id)->first();
        $id = $request->id;
        $list_id = $request->list_id;
        $data['error'] = false;
        $today = Carbon::now()->format('Y-m-d h:i');

         if($request->date_event < $today){
            $data = array(
                'error'=>true,
                'date_event'=>'Date event cannot be less than today',
            );
            return response()->json($data);
        } 

        $rules = array(
            'id'=>['required','numeric'],
            'list_id'=>['required','numeric'],
            'day'=>['required'],
            'hour'=>['required'],
            'date_event'=>['required'],
        );

        $validator = Validator::make($request->all(),$rules);
        $errors = $validator->errors();

        if($validator->fails()){
            $data = array(
                'id'=>$errors->first('id'),
                'list_id'=>$errors->first('list_id'),
                'day'=>$errors->first('day'),
                'hour'=>$errors->first('hour'),
                'date_event'=>$errors->first('date_event'),
                'error'=>true
            );
            return response()->json($data);
        }

        $event = Reminder::where('id',$id)->update([
            'days'=>$request->day,
            'hour_time'=>$request->hour,
        ]);

        if($event == true){
            $list = UserList::where('id',$list_id)->update(['event_date'=>$request->date_event]);
        } else {
            $data['message'] = 'Error, unable to update event date';
            return response()->json($data);
        }

        // if reminder and lists updated successfully
        if($list == true){
            $remindercustomer = ReminderCustomers::where('reminder_id',$id)->get();
        } else {
            $data['message'] = 'Error, unable to update event';
        } 

        if($remindercustomer->count() > 0){
           foreach($remindercustomer as $row){
                $wa_sender_id = $row->id_wa;
                if($wa_sender_id !== null){
                    $update_send_target = ReminderCustomers::where('id',$row->id)->update(['id_wa'=>null, 'status'=>0]);
                } else {
                    $update_send_target = null; //nothing to update
                }
            }
        } else {
            $update_send_target = null; //nothing to update
        }

        if($update_send_target == true || $update_send_target == null){
            $data['message'] = 'Your event reminder has been updated';
        } else {
            $data['message'] = 'Error, unable to update event';
        }

        return response()->json($data);

    }

    /* Change reminder and reminder-customer status */
    public function setEventStatus($id_reminder,$status){

        if(empty($id_reminder) && empty($status))
        {
             return redirect('event')->with('error','Error! Please do not change the values');
        }

        if($status == 0 || $status == 1){
           $pass = true;
        } else {
           return redirect('event')->with('error','Error! Please do not change the values');
        }
 
        /* From on to off */
        if($status == 1){
            $turn = 0;
            $turn_customer = 3;
        } else {
            $turn = 1;
            $turn_customer = 0;
        }

        $reminder = Reminder::where('id','=',$id_reminder)->update([
            'status'=>$turn
        ]);

        /* if correct then reminder's status updated */
        if($reminder == true){
            $remindercustomer =  ReminderCustomers::where([
                ['reminder_id','=',$id_reminder],
            ])->whereIn('status', [0,3])->update(['status'=> $turn_customer]);
        } else {
            return redirect('event')->with('error','Error-001! Unable to change reminder status');
        }

        /* if correct then reminder-customer's status updated */
        if($remindercustomer == true){
            return redirect('event')->with('message','Your reminder status has been change');
        } else {
            /* if there is no status = 0 */
            return redirect('event')->with('warning','Warning! Your reminder status just changed, but nothing reminder for customer');
        }
    }

    function importCSVEvent(Request $request)
    {
        $id_list = $request->list_id_import;
        try{
            $id_list = decrypt($id_list);
        }catch(DecryptException $e){
            return redirect('event');
        }

        $file = $request->file('csv_file');
        Excel::import(new UsersImport($id_list), $file);
    }


    function loadEvent(Request $request){
      $id = Auth::id();
      $events = Reminder::where([['reminders.user_id',$id],['reminders.is_event','=',1],['reminders.campaign_id','=',$request->campaign_id]])
                // ->select('lists.label','lists.created_at','reminders.id AS id_reminder','reminders.*')
                ->orderBy('id','desc')
                ->get();
      $arr['view'] =(string) view('event.load-event')
                      ->with([
                        "events"=>$events,
                      ]);

      return $arr;
    }

    public function deleteEvent(Request $request)
    {
        $id = $request->id;

        try{
          Reminder::find($id)->delete();
          $err['status'] = 'success';
          $err['message'] = 'Data catalog telah dihapus';  
        }catch(\Illuminate\Database\QueryException $e){
          $err['status'] = FALSE;
          $err['message'] = 'Data catalog gagal dihapus';
        }


        return response()->json($err);
    }

/* End event controller */
}
