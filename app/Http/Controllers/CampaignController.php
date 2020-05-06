<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\BroadCastController;
use App\UserList;
use App\Customer;
use App\Campaign;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use App\Message;
use App\Rules\CheckDateEvent;
use App\Rules\CheckValidListID;
use App\Rules\CheckEventEligibleDate;
use App\Rules\CheckBroadcastDate;
use App\Rules\CheckExistIdOnDB;
use App\Rules\EligibleTime;
use DB;
use Carbon\Carbon;
use App\Helpers\ApiHelper;
use App\PhoneNumber;
use App\Server;
use Storage;

class CampaignController extends Controller
{
    public function index()
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data['lists'] = $lists;
      return view('campaign.campaign',$data);
    }

    public function sendTestMessage(Request $request) 
    {
			// dd($_FILES["imageWA"]);
			
			$rules = array(
					'phone'=>['required','max:255'],
					'message'=>['required','max:4095'],
					'imageWA'=>['max:1024'],
			);
			$validator = Validator::make($request->all(),$rules);
			$err = $validator->errors();

			if($validator->fails()){
					$error = array(
						'status'=>'error',
						'phone'=>$err->first('phone'),
						'msg'=>$err->first('message'),
						'image'=>"image maximum size 1MB",
					);
					return response()->json($error);
			}
			if($request->hasFile('imageWA')) {
				$image_size = getimagesize($request->file('imageWA'));
				$imagewidth = $image_size[0];
				$imageheight = $image_size[1];
				if(($imagewidth > 2000) || ($imageheight > 2000) ){
						$error = array(
							'status'=>'error',
							'phone'=>"",
							'msg'=>"",
							'image'=>"image width or image height more than 2000px",
						);
						return response()->json($error);
				}
			}
			$user = Auth::user();

			$phoneNumber = PhoneNumber::where("user_id",$user->id)->first();
			$key = $phoneNumber->filename;


			if ($phoneNumber->mode == 0) {
				$server = Server::where('phone_id',$phoneNumber->id)->first();
				if(is_null($server)){
					$error = array(
						'status'=>'error',
						'phone'=>"Contact Administrator",
						'msg'=>"",
						'image'=>"",
					);
					return response()->json($error);
				}
			}

			/*if ($user->email=="activomnicom@gmail.com") {
				ApiHelper::send_message_android(env('BROADCAST_PHONE_KEY'),$request->message,$request->phone,"reminder");
			}
			else {*/
				if($request->hasFile('imageWA')) {
					//save ke temp local dulu baru di kirim 
					$folder = $user->id."/send-test-message/";
					Storage::disk('s3')->put($folder."temp.jpg",file_get_contents($request->file('imageWA')), 'public');
					sleep(1);
					dd(Storage::disk('s3')->get($folder."temp.jpg"));
					$url = Storage::disk('s3')->url($folder."temp.jpg");
					if ($phoneNumber->mode == 0) {
						ApiHelper::send_image_url_simi($request->phone,curl_file_create(
							$_FILES["imageWA"]["tmp_name"],
							$_FILES["imageWA"]["type"],
							$_FILES["imageWA"]["name"]
						),$request->message,$server->url);
					}
					else {
						ApiHelper::send_image_url($request->phone,$url,$request->message,$key);

						$arr = array(
							'url'=>$url,
							'status'=>"success",
						);
						return response()->json($arr);
					}
				}
				else {
					// ApiHelper::send_message($request->phone,$request->message,$key);
					$message_send = new Message;
					$message_send->phone_number=$request->phone;
					$message_send->message=$request->message;
					if ($phoneNumber->mode == 0) {
						$message_send->key=$server->url;
						$message_send->status=6;
					}
					if ($phoneNumber->mode == 1) {
						$message_send->key=$key;
						$message_send->status=7;
					}
					$message_send->customer_id=0;
					$message_send->save();

				}
			// }
			// return "success";
			$arr = array(
				'status'=>"success",
			);
			return response()->json($arr);
		}
		
		public function CreateCampaign() 
    {
      $userid = Auth::id();
      $data = array();
      $list_users = UserList::where('user_id',$userid)->select('label','id')->get();

      if($list_users->count() > 0)
      {
        foreach($list_users as $row)
        {
          $customer = Customer::where('list_id',$row->id)->get();
          $data[] = array(
            'id'=>$row->id,
            'label'=>$row->label,
            'customer_count'=>$customer->count(),
          );
        }
      }

      $data = array(
          'lists'=>$data,
      );

      return view('campaign.create-campaign',$data);
    }

    public function SaveCampaign(Request $request)
    {
			if($request->hasFile('imageWA')) {
				$image_size = getimagesize($request->file('imageWA'));
        // $image_file_size = (int)number_format($request->file('imageWA')->getSize() / 1024, 2);
				$imagewidth = $image_size[0];
				$imageheight = $image_size[1];
				if(($imagewidth > 2000) || ($imageheight > 2000) )
        {
            $error = array(
              'err'=>'imgerr',
            );
            return response()->json($error);
				}
			}
    
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
            'message'=>['required','max:65000'],
						'imageWA'=>['mimes:jpeg,jpg,png,gif','max:4096'],
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
							'image'=>$err->first('imageWA'),
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
            'message'=>['required','max:65000'],
						'imageWA'=>['mimes:jpeg,jpg,png,gif','max:4096'],
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
							'image'=>$err->first('imageWA'),
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
          'message'=>['required','max:65000'],
          'imageWA'=>['mimes:jpeg,jpg,png,gif','max:4096'],
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
							'image'=>$error->first('imageWA'),
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
          if(getMembership(Auth()->user()->membership) > 1)
          {
            $campaign = Campaign::where([['campaigns.user_id',$userid],['campaigns.type','<',3]])
                      ->join('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->get();
          }
          else
          {
            $campaign = Campaign::where('campaigns.user_id',$userid)
                      ->whereIn('campaigns.type',[1,2])
                      ->join('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->get();
          }
        }
        elseif($type <> null)
        { 
          if(getMembership(Auth()->user()->membership) > 1)
          {
            $real_type = $type;
          }
          else
          {
            $real_type = null;
          }

          $campaign = Campaign::where([['campaigns.user_id',$userid],['campaigns.type','=',$real_type]])
                      ->join('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->get();
        }
        else
        {
          if(getMembership(Auth()->user()->membership) > 1)
          {
            $campaign = Campaign::where([['campaigns.name','like','%'.$search.'%'],['campaigns.user_id',$userid],['campaigns.type','<',3]]);
          }
          else
          {
            $campaign = Campaign::where([['campaigns.name','like','%'.$search.'%'],['campaigns.user_id',$userid]])->whereIn('campaigns.type',[1,2]); 
          }

          $campaign->join('lists','lists.id','=','campaigns.list_id')
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

                  $total_message = $this->broadcastCampaign($row->id,'=',0)->count();
                  $total_delivered = $this->broadcastCampaign($row->id,'>',0)->count();

                  $data[] = array(
                      'type'=>2,
                      'id'=>$broadcast->id,
                      'campaign_id'=>$row->id,
                      'campaign' => $row->name,
                      'date_send' => $broadcast->day_send,
                      'day_send' => Date('M d, Y',strtotime($broadcast->day_send)),
                      'sending' => Date('H:i',strtotime($broadcast->hour_time)),
                      'label' => $label,
                      'created_at' => Date('M d, Y',strtotime($row->created_at)),
                      'total_message' => $total_message,
                      'sent_message' => $total_delivered,
                      'messages' => $broadcast->message,
                  );
                }
                else //REMINDER
                {
                    if($row->type == 0)
                    {
                        $reminder = Reminder::where([['campaign_id',$row->id],['is_event',1],['tmp_appt_id','=',0]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();

                        $total_message = $this->campaignsLogic($row->id,$userid,1,'=',0);
                        $total_delivered = $this->campaignsLogic($row->id,$userid,1,'>',0);
                    }
                    else
                    {           
                        $reminder = Reminder::where([['campaign_id',$row->id],['is_event',0],['tmp_appt_id','=',0]])->join('lists','lists.id','=','reminders.list_id')->select('reminders.*','lists.label','lists.created_at')->first();

                        $total_message = $this->campaignsLogic($row->id,$userid,0,'=',0); 
                        $total_delivered = $this->campaignsLogic($row->id,$userid,0,'>',0);
                    } 

                    if(!is_null($reminder))
                    {
                        $days = (int)$reminder->days;
                        $total_template = Reminder::where('campaign_id',$row->id)->get()->count();

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
                            'total_template' => $total_template,
                            'total_message' => $total_message->count(),
                            'sent_message' => $total_delivered->count()
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
                            'total_template' => $total_template,
                            'total_message' => $total_message->count(),
                            'sent_message' => $total_delivered->count()
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
                        'sending_time' => '-',
                        'label' => $row->label,
                        'created_at' => Date('M d, Y',strtotime($row->created_at)),
                        'total_message' => 0,
                        'sent_message' => 0,
                        'total_template' => 0
                      );
                    }

                } //END IF CAMPAIGN

            }//ENDFOREACH
        }

        return view('campaign.campaign-search',['data'=>$data]);
    }

    private function campaignsLogic($campaign_id,$userid,$is_event,$cond,$status)
    {
        $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status',$cond,$status]])
          ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.id AS rcid','reminder_customers.status')
          ->get();

        return $campaigns;
    }

    public function listBroadcastCampaign(Request $request)
    {
        $userid = Auth::id();
        $campaign_id = $request->campaign_id;
        $active = $request->active;

        if($active == 1)
        {
            $campaigns = $this->broadcastCampaign($campaign_id,'=',0);
        }
        else
        {
            $campaigns = $this->broadcastCampaign($campaign_id,'>',0);
        }
       
        return view('campaign.list_broadcast_table',['active'=>$active,'campaigns'=>$campaigns]);
    }

    private function broadcastCampaign($campaign_id,$cond,$status)
    {
        $userid = Auth::id();
        $campaigns = BroadCastCustomers::where([['broad_casts.campaign_id',$campaign_id],['broad_casts.user_id',$userid],['broad_cast_customers.status',$cond,$status]])
                  ->join('broad_casts','broad_casts.id','=','broad_cast_customers.broadcast_id')
                  ->join('customers','customers.id','=','broad_cast_customers.customer_id')
                  ->select('customers.name','customers.telegram_number','broad_casts.day_send','broad_casts.hour_time','broad_cast_customers.id AS bcsid','broad_cast_customers.status')
                  ->get();

        return $campaigns;
    }

    public function listCampaign($campaign_id,$is_event,$active)
    {
        /*
          FOR ACTIVSCHEDULE & EVENT
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
            $campaigns = $this->campaignsLogic($campaign_id,$userid,$is_event,'=',0);
          }
          else
          {
            $campaigns = $this->campaignsLogic($campaign_id,$userid,$is_event,'>',0);
          }
        }

        return view('campaign.list_campaign',['campaign_id'=>$campaign_id,'campaign_name'=>$checkid->name,'active'=>$active,'campaigns'=>$campaigns,'is_event'=>$is_event]);
    }

    public function getCampaignAjaxTable(Request $request)
    {
        $draw = $request->draw;
        $search = $request->search['value'];
        $start = $request->start;
        $length = $request->length;
        $order = $request->order[0]['column'];
        $dir = $request->order[0]['dir'];
        $active = $request->active;
        $campaign_id = $request->campaign_id;
        $is_event = $request->is_event;
        $userid = Auth::id();

        if($active == 1)
        {
          $total_page = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','=',0]])
          ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.id AS rcid')
          ->get()
          ->count();

          // QUEUE
          if($search == null)
          {
             $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','=',0]])
              ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
              ->join('customers','customers.id','=','reminder_customers.customer_id')
              ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.id AS rcid','reminder_customers.status')
              ->take($length)
              ->skip($start)
              ->orderBy('reminder_customers.id',$dir)
              // ->distinct()
              ->get();
          }
          else
          {
            $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','=',0]])
              ->where(function($query) use($search){
                $query->where('customers.name','LIKE','%'.$search.'%')
                ->orWhere('reminders.event_time','LIKE','%'.$search.'%')
                ->orWhere('reminders.days','=',$search)
                ->orWhere('customers.telegram_number','=',$search)
                ;
              }) 
              ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
              ->join('customers','customers.id','=','reminder_customers.customer_id')
              ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.id AS rcid','reminder_customers.status')
              ->take($length)
              ->skip($start)
              ->orderBy('reminder_customers.id',$dir)
              // ->distinct()
              ->get();
          }
        }
        else
        {
          // DELIVERED
           $total_page = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','>',0]])
          ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.status')
          ->get()->count();

          if($search == null)
          {
            $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','>',0]])
            ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.status')
            ->take($length)
            ->skip($start)
            ->orderBy('reminder_customers.id',$dir)
            ->get();
          }
          else
          {
            $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status','>',0]])
             ->where(function($query) use($search){
                $query->orWhere('customers.name','LIKE','%'.$search.'%')
                ->orWhere('reminders.event_time','=',$search)
                ->orWhere('reminders.days','=',$search)
                ->orWhere('customers.telegram_number','=',$search)
                ;
            }) 
            ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminders.campaign_id','reminders.event_time','reminders.days','customers.name','customers.telegram_number','customers.id','reminder_customers.status')
            ->take($length)
            ->skip($start)
            ->orderBy('reminder_customers.id',$dir)
            ->get();
          }
        }

        $data = array();
        if($campaigns->count() > 0)
        {
            $number = 1;
            if($active == 1)
            {

              if($is_event == 1)
              {
                // EVENT
                foreach($campaigns as $rows)
                {
                    $data[] = array(
                      0=>$number,
                      1=>$rows->event_time,
                      2=>'H'.$rows->days,
                      3=>$rows->name,
                      4=>$rows->telegram_number,
                      5=>'<a id='.$rows->rcid.' class="icon-cancel"></a>',
                    );
                    $number++;
                }
              }
              else
              {
                // AUTORESPONDER
                foreach($campaigns as $rows)
                {
                    $data[] = array(
                      0=>$number,
                      1=>'H'.$rows->days,
                      2=>$rows->name,
                      3=>$rows->telegram_number,
                      4=>'<a id='.$rows->rcid.' class="icon-cancel"></a>',
                    );
                    $number++;
                }
              }
              
            }
            else
            {
              // DELIVERED
              if($is_event == 1)
              {
                 foreach($campaigns as $rows)
                 {
                    if($rows->status == 1)
                    {
                      $status = 'Success';
                    }
                    elseif($rows->status == 2)
                    {
                      $status = 'Phone Offline';
                    }
                    elseif($rows->status == 3)
                    {
                      $status = 'Phone Not Available';
                    }
                    else
                    {
                      $status = 'Cancelled';
                    }

                    $data[] = array(
                      0=>$number,
                      1=>$rows->event_time,
                      2=>'H'.$rows->days,
                      3=>$rows->name,
                      4=>$rows->telegram_number,
                      5=>$status,
                    );
                    $number++;
                 } //ENDFOREACH
              }
              else
              {
                 foreach($campaigns as $rows)
                 {
                    if($rows->status == 1)
                    {
                      $status = 'Success';
                    }
                    elseif($rows->status == 2)
                    {
                      $status = 'Phone Offline';
                    }
                    elseif($rows->status == 3)
                    {
                      $status = 'Phone Not Available';
                    }
                    else
                    {
                      $status = 'Cancelled';
                    }

                    $data[] = array(
                      0=>$number,
                      1=>'H'.$rows->days,
                      2=>$rows->name,
                      3=>$rows->telegram_number,
                      4=>$status,
                    );
                    $number++;
                 } //ENDFOREACH
              }
             
            }
           
        }

        $result['draw'] =  $draw;
        $result['data'] =  $data;
        $result['recordsTotal'] = $campaigns->count();
        $result['recordsFiltered'] = $total_page;

        return response()->json($result);
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

    public function listDeleteCampaign(Request $request)
    {
        $userid = Auth::id();
        $is_broadcast = $request->is_broadcast;

        if($is_broadcast == 1)
        {
          $broadcast_customer_id = $request->broadcast_customer_id;
          $customer = BroadCastCustomers::find($broadcast_customer_id);
        }
        else
        {
          $reminder_customer_id = $request->reminder_customer_id;
          $customer = ReminderCustomers::find($reminder_customer_id);
        }

        try
        {
            $customer->status = 4;
            $customer->save();
            $data['success'] = 1;
            $data['broadcast'] = $is_broadcast;
        }
        catch(Exception $e)
        {
            $data['success'] = 0;
        }

        return response()->json($data);
    }

/* end controller */
}
