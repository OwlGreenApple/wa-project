<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
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
use Carbon\Carbon;
use App\Helpers\ApiHelper;
use App\PhoneNumber;
use App\Server;
use Storage;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
      $userid = Auth::id();
      $data = array();
      $paging = 25;
      $type = $request->type;
      $search = $request->search;

      if(getMembership(Auth::user()->membership) > 3)
      {
        $campaign_type = [1,2];
      }
      else
      {
        $campaign_type = [1];
      }

      if($type == null || $type == 'all')
      {
          $campaign = Campaign::where('campaigns.user_id',$userid)
                ->whereIn('campaigns.type',$campaign_type)
                ->leftJoin('lists','lists.id','=','campaigns.list_id')
                ->orderBy('campaigns.id','desc')
                ->select('campaigns.*','lists.label')
                ->paginate($paging);
      } 

      if($type <> null && $type <> 'all')
      {
          $campaign = Campaign::where('campaigns.user_id',$userid)
                      ->where('campaigns.type',$type)
                      ->leftJoin('lists','lists.id','=','campaigns.list_id')
                      ->orderBy('campaigns.id','desc')
                      ->select('campaigns.*','lists.label')
                      ->paginate($paging);
      }

      if($search <> null)
      {
          $campaign = Campaign::where([['campaigns.name','like','%'.$search.'%'],['campaigns.user_id',$userid]])->whereIn('campaigns.type',$campaign_type)
            ->leftJoin('lists','lists.id','=','campaigns.list_id')
            ->orderBy('campaigns.id','desc')
            ->select('campaigns.*','lists.label')
            ->paginate($paging); 
      }

      $data['lists'] = displayListWithContact($userid);
      $data['paginate'] = $campaign;
      $data['campaign'] = $campaign;
      $data['broadcast'] = new BroadCast;
      $data['userlist'] = new UserList;
      $data['campaign_controller'] = new CampaignController;
      $data['autoschedule'] = new Reminder;
      $data['userid'] = $userid;

      if($request->ajax())
      {
        return view('campaign.index',$data);
      }
      return view('campaign.campaign',$data);
    }

    public function sendTestMessage(Request $request) 
    {
			// dd(Image::make(file_get_contents('https://omnilinkz.s3.us-west-2.amazonaws.com/banner/Rizky-6/2004181003-967.jpg')));
			// dd($_FILES["imageWA"]);

			$rules = array(
					'phone'=>['required','max:255']
			);

      if($request->edit_message == null)
      {
          $rules['message'] = ['required','max:65000'];
          $message = $request->message;
      }
      else
      {
          $rules['edit_message'] = ['required','max:65000'];
          $message = $request->edit_message;
      }

			if($request->hasFile('imageWA')) {
        $rules['imageWA'] = ['max:1024'];
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

      $validator = Validator::make($request->all(),$rules);
      $err = $validator->errors();

      if($validator->fails()){

          if($err->first('message') == null)
          {
            $err_msg = $err->first('edit_message');
          }
          else
          {
            $err_msg = $err->first('message');
          }

          $error = array(
            'status'=>'error',
            'phone'=>$err->first('phone'),
            'msg'=>$err_msg,
            'image'=>$err->first('imageWA'),
          );
          return response()->json($error);
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
					$url = Storage::disk('s3')->url($folder."temp.jpg");
					if ($phoneNumber->mode == 0) {
						ApiHelper::send_image_url_simi($request->phone,curl_file_create(
							$_FILES["imageWA"]["tmp_name"],
							$_FILES["imageWA"]["type"],
							$_FILES["imageWA"]["name"]
						),$message,$server->url);
					}
					else {
						ApiHelper::send_image_url($request->phone,$url,$message,$key);

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
					$message_send->message= $message;
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
      $data = array(
          'lists'=>displayListWithContact($userid),
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
            'hour'=>['required','date_format:H:i',new EligibleTime($request->event_time,$request->day)],
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
          'hour'=>['required','date_format:H:i',new EligibleTime($request->date_send,0)],
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

				// CreateBroadcast::dispatch(serialize($request));
				
				// $data['message'] = "Your broadcast has been created";
				// return response()->json($data);
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

      if(is_null($campaign))
      {
        return redirect('home');
      }

      if ($campaign->user_id<>$user_id){
        return "Not Authorized";
      }
      $lists = UserList::where('user_id',$user_id)->get();
      $current_list = UserList::where('id',$campaign->list_id)->select('label')->first();
      $reminder = Reminder::where('campaign_id',$campaign_id)->first();
      $data['lists'] = $lists;
      $data['campaign_id'] = $campaign_id;
      $data['campaign_name'] = $campaign->name;
      $data['currentlist'] = $current_list->label;
      $data['currentlistid'] = $campaign->list_id;
      $data['published'] = $campaign->status;
      $data['date_event'] = $reminder->event_time;
      $data['list_id'] = $campaign->list_id;
      return view('event.add-message-event',$data);
    }

    public function campaignsLogic($campaign_id,$userid,$is_event,$cond,$status)
    {
        $campaigns = ReminderCustomers::where([['reminders.campaign_id',$campaign_id],['reminders.is_event',$is_event],['reminders.user_id',$userid],['reminder_customers.status',$cond,$status]])
          ->join('reminders','reminders.id','=','reminder_customers.reminder_id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.campaign_id','reminders.message','reminders.event_time','reminders.days','customers.name','customers.email','customers.telegram_number','customers.id','reminder_customers.id AS rcid','reminder_customers.status','reminder_customers.updated_at')
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

    public function broadcastCampaign($campaign_id,$cond,$status)
    {
        $userid = Auth::id();
        $campaigns = BroadCastCustomers::where([['broad_casts.campaign_id',$campaign_id],['broad_casts.user_id',$userid],['broad_cast_customers.status',$cond,$status]])
                  ->join('broad_casts','broad_casts.id','=','broad_cast_customers.broadcast_id')
                  ->leftJoin('customers','customers.id','=','broad_cast_customers.customer_id')
                  ->select('customers.name','customers.telegram_number','broad_casts.day_send','broad_casts.hour_time','broad_cast_customers.id AS bcsid','broad_cast_customers.status','broad_cast_customers.updated_at')
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

        $checkid = Campaign::where([['campaigns.id',$campaign_id],['campaigns.user_id',$userid]])
                    ->join('lists','lists.id','=','campaigns.list_id')
                    ->select('campaigns.name','lists.label','lists.id')
                    ->first();

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

        return view('campaign.list_campaign',['campaign_id'=>$campaign_id,'campaign_name'=>$checkid->name,'active'=>$active,'campaigns'=>$campaigns,'is_event'=>$is_event,'list_name'=>$checkid->label,'list_id'=>$checkid->id]);
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
        $reminders = Reminder::where([['campaign_id',$campaign->id],['user_id',$user_id]])->get();

        if($reminders->count() > 0)
        {
          foreach($reminders as $reminder) {
            $remindercustomer = ReminderCustomers::where('reminder_id','=',$reminder->id)->delete();
          }

          try {
            Reminder::where([['campaign_id',$campaign->id],['user_id',$user_id]])->delete();
            $campaign->delete();
            return response()->json(['message'=>'Your campaign has been deleted successfully']);
          }
          catch(Exception $e)
          {
             return response()->json(['message'=>'Sorry, unable to delete , contact administrator']);
          }
        }
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
        catch(QueryException $e)
        {
          //dd($e->getMessage());
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
