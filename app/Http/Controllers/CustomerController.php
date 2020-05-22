<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use App\Customer;
use App\UserList;
use Carbon\Carbon;
use App\Reminder;
use App\ReminderCustomers;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Sender;
use App\Additional;
use App\PhoneNumber;
use App\Server;
use App\Countries;
use App\Message;
use App\Console\Commands\SendWA as SendMessage;
use App\Helpers\ApiHelper;
use App\Rules\CheckWANumbers;

class CustomerController extends Controller
{

    public function subscriber(Request $request, $link_list)
    {
      $check_link = UserList::where([
          ['name','=',$link_list],
          ['status','=',1],
      ])->first();

      if(empty($link_list)){
        return redirect('/');
      } elseif(is_null($check_link)) {
        return redirect('/');
      } else {
            $list = UserList::where('name',$link_list)->first();
            $additional = Additional::where('list_id',$list->id)->get();
            $data = array();
            $arr = array();
            $data['fields'] = array();

            if($additional->count() > 0)
            {
                foreach($additional as $row)
                {
                   if($row->id_parent == 0){
                        $data['fields'][] = $row;
                   } 
                }
            }

            if(count($data['fields']) > 0)
            {
                foreach($data['fields'] as $col)
                {
                     # count if name has child or not
                     $doption = Additional::where([['list_id',$list->id],['id_parent',$col->id]])->get();

                     $colname = $col->name;

                     if($doption->count() > 0)
                     {
                         foreach($doption as $rows)
                         {
                            $arr[(int)$col->is_optional][$colname][$col->is_field][] = $rows->name;
                         }
                     } 
                     else 
                     {
                            $arr[(int)$col->is_optional][$colname][$col->is_field] = $col;
                     }
                } 

            }

        $data = [
          'id'=>encrypt($list->id),
          'label_name'=>$list->label_name,
          'label_last_name'=>$list->label_last_name,
          'label_phone'=>$list->label_phone,
          'label_email'=>$list->label_email,
          'checkbox_email'=>$list->checkbox_email,
          'checkbox_lastname'=>$list->checkbox_lastname,
          'content'=>$list->content,
          'listname'=>$link_list,
          'pixel'=>$list->pixel_text,
          'additional'=>$arr,
          'btn_message'=>$list->button_subscriber,
          'link_add_customer'=>url($link_list)
        ];

        return view('register-customer',$data);
      }
    }

    public function saveSubscriber(Request $request)
    {
        $listname = $request->listname;
        $phone_number = $request->code_country.$request->phone_number;
        $req = $request->all();

        $list = UserList::where('name','=',$listname)->first();
        $today = Carbon::now();
        $valid_customer = false;

        if(isset($req['data']))
        {
            $addt = json_encode($req['data']);
        } 
        else {
            $addt = null;
        }

        // Filter to avoid unavailable link 
        if(is_null($list)){
            return redirect('/');
        } 
        else {
            // $customer = new Customer;
            // $customer->user_id = $list->user_id;
            // $customer->list_id = $list->id;
            // $customer->name = $request->subscribername;
            // $customer->email = $request->email;
            // $customer->telegram_number = $phone_number;
            // $customer->additional = $addt;
            // if ($list->is_secure) {
              // $customer->status = 0;
            // }
            // $customer->save();

            if($request->overwrite == null && $request->listedit == 1)
            {
                $check_phone = $this->checkDuplicateSubscriberPhone($phone_number,$list->id);
                if($check_phone == true)
                {
                  return response()->json(['duplicate'=>1]);
                }
            }

						$status = 1;
            if ($list->is_secure) {
              $status = 0;
            }

            //NORMAL CASE
            if($request->data_update <> null)
            {
              $customer = Customer::find($request->data_update);
              if($request->phone_number == null)
              {
                $customer->name = $request->subscribername;
                $customer->last_name = $request->last_name;
                $customer->email = $request->email;
                $customer->code_country = $request->data_country;
                $customer->status = 1;
              }
              else
              {
                $customer->name = $request->subscribername;
                $customer->last_name = $request->last_name;
                $customer->email = $request->email;
                $customer->telegram_number = $phone_number;
                $customer->code_country = $request->data_country;
                $customer->status = 1;
              }

              try
              {
                  $customer->save();
                  $data['update'] = true;
                  $data['message'] = 'Success, your contact has updated';
              }
              catch(QueryException $e)
              {
                  $data['update'] = false;
                  $data['message'] = 'Sorry, our system is too busy';
              }
              return response()->json($data);
            }

            // AVAILABLE DATA CASE ON LIST EDIT UNDER IMPORT
            if($request->overwrite == 1)
            {
              $customer_phone = Customer::where([['list_id',$list->id],['telegram_number',$phone_number]]);

              $update = array(
                'name' => $request->subscribername,
                'email'=> $request->email,
                'code_country'=>$request->data_country,
                'status'=> 1
              );

              try
              {
                $customer_phone->update($update);
                $data['success'] = true;
                $data['message'] = 'Success, your contact has been overwritten';
              }
              catch(QueryException $e)
              {
                $data['success'] = false;
                $data['message'] = 'Sorry, our system is too busy';
              } 
              return response()->json($data);
            }
            else
            {
              $customer = Customer::create([
                 'user_id'  => $list->user_id,
                 'list_id'  => $list->id,
                 'name'     => $request->subscribername,
                 'last_name' => $request->last_name,
                 'telegram_number'=>$phone_number,
                 'code_country'=>$request->data_country,
                 'email'=> $request->email,
                 'status'=> $status,
              ]);
              $customer_id = $customer->id;
              $customer_join = $customer->created_at;
            }

            /*
            Kalo is_secure maka akan dikirim langsung message wa nya 
            */
            if ($list->is_secure) {
							$ret = json_decode($this->sendListSecure($list->id,$customer_id,$request->subscribername,$list->user_id,$list->name,$phone_number),1);
              dd($ret);

							if($ret['success'] == false)
              {
								$data['success'] = false;
								$data['message'] = 'test';
								return response()->json($data);
							}
            }

            // if customer successful sign up 
            if($customer->save()){
               $user_id = $list->user_id;
               $list_id = $list->id;
               return $this->addSubscriber($list_id,$customer_id,$customer_join,$user_id);
            } 
            else {
              $data['success'] = false;
              $data['message'] = 'Sorry, our system is too busy';
            }
            return response()->json($data);
        }
    }
		
		function sendListSecure($list_id,$customer_id,$subscribername,$user_id,$list_name,$phone_number)
		{
			$phoneNumber = PhoneNumber::where("user_id",$user_id)->first();
			$key = $phoneNumber->filename;

			//pengecekan klo pake simi
			if(env('APP_ENV') !== 'local')
			{
				if ($phoneNumber->mode == 0) {
					$server = Server::where('phone_id',$phoneNumber->id)->first();
					if(is_null($server)){
						$data['success'] = false;
						$data['message'] = 'Sorry, our system is too busy';
						// return response()->json($data);
						return response()->json($data);
					}
				}
			}

			$reminder = Reminder::
									where("is_event",0)
									->where("days",0)
									->where("list_id",$list_id)
									->first();
			$message = "";
			if (!is_null($reminder)){
				$message = $reminder->message;
				$message = str_replace( "[NAME]" , $subscribername, $message);
				// $message = str_replace( "[REPLY_CHAT]" , "whatsapp://send/?phone=".$phoneNumber->phone_number."&text=" . "Hi Nama saya ".$request->subscribername.", saya bergabung digroup ini", $message);

				$message = str_replace( "[START]" , env("APP_URL")."link/activate/".$list_name."/".$customer_id, $message);
				$message = str_replace( "[UNSUBS]" , env("APP_URL")."link/unsubscribe/".$list_name."/".$customer_id, $message);
			}
			// ApiHelper::send_message($phone_number,$message,$key);
			$message_send = new Message;
			$message_send->phone_number=$phone_number;
			$message_send->message=$message;
			if ($phoneNumber->mode == 0) {
				$message_send->key=$server->url;
				$message_send->status=8;
			}
			if ($phoneNumber->mode == 1) {
				$message_send->key=$key;
				$message_send->status=9;
			}
			$message_send->customer_id=$customer_id;

      try{
        $message_send->save();
        $data['success'] = true;
        $data['message'] = "Data saved";
      }
      catch(QueryException $e)
      {
        $data['success'] = false;
        $data['message'] = $e->getMessage();
      }
			return response()->json($data);
		}

    private function checkDuplicateSubscriberPhone($wa_number,$list_id)
    {
        $customer = Customer::where([
          ['telegram_number','=',$wa_number],
          ['list_id','=',$list_id]
        ])->first();

        if(is_null($customer))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function addSubscriber($list_id,$customer_id,$customer_join,$user_id)
    {
        $reminder = Reminder::where([['list_id','=',$list_id],['user_id','=',$user_id],['status','=',1],])->get();
        $confirmation = UserList::find($list_id);

        if($reminder->count() > 0)
        {
           //EVENT
          foreach($reminder as $row)
          {
              $is_event = $row->is_event;

              if($is_event == 1 || $is_event == 2)
              {
                  $today = Carbon::now()->toDateString();
                  $days = (int)$row->days;
                  $event_date = Carbon::parse($row->event_time);

                  if($days < 0){
                    $days = abs($days);
                    $event_date->subDays($days);
                  } 
                  else {
                    $event_date->addDays($days);
                  }

                  $event_date = $event_date->toDateString();

                  if($event_date >= $today){
                      $reminder_customer = new ReminderCustomers;
                      $reminder_customer->user_id = $user_id;
                      $reminder_customer->list_id = $list_id;
                      $reminder_customer->reminder_id = $row->id;
                      $reminder_customer->customer_id = $customer_id;
                      $reminder_customer->save();
                  } 
              } //END IF IS_EVENT
              else
              //AUTO RESPONDER
              {
                  $days = (int)$row->days;
                  $after_sum_day = Carbon::parse($customer_join)->addDays($days);
                  $validday = $after_sum_day->toDateString();
                  $createdreminder = Carbon::parse($row->created_at)->toDateString();

                  if($validday >= $createdreminder){
                      $reminder_customer = new ReminderCustomers;
                      $reminder_customer->user_id = $user_id;
                      $reminder_customer->list_id = $list_id;
                      $reminder_customer->reminder_id = $row->id;
                      $reminder_customer->customer_id = $customer_id;
                      $reminder_customer->save(); 
                  } 
              }
          }//END FOREACH
        }

        //BROADCAST 
        $broadcast = BroadCast::where([['list_id',$list_id],['user_id',$user_id]],['status',1])->get();

        if($broadcast->count() > 0)
        {
          foreach($broadcast as $row)
          {
              $broadcastcustomer = new BroadCastCustomers;
              $broadcastcustomer->broadcast_id = $row->id;
              $broadcastcustomer->customer_id = $customer_id;
              $broadcastcustomer->save();
          }
        }

        // DETERMINE WHETHER APPOINTMENT OR NOT
        $userid = Auth::id();
        if($userid <> null)
        {
          $is_appointment = 1;
        }
        else
        {
          $is_appointment = 0;
        }
        
        $data['success'] = true;
        $data['message'] = $confirmation->message_conf;
        $data['is_appointment'] = $is_appointment;
        return response()->json($data);
    }

    // redirect page

    public function link_activate($list_name,$customer_id)
    {
      $list = UserList::where('name','=',$list_name)->first();
      $customer = Customer::find($customer_id);
      if (!is_null($customer)){
        if ($customer->list_id == $list->id ) {
          $customer->status = 1;
          $customer->save();
        }
      }
			$message = "";
			if (!is_null($list)){
				$message = $list->start_custom_message;
				$message = str_replace("[LIST_NAME]",$list->label,$message);
			}
      // return redirect($list->name)->with('message',$message)
			return view('layouts.subscribe',['label'=>$list->label]);
			;
    }

    public function link_unsubscribe($list_name,$customer_id)
    {
      $list = UserList::where('name','=',$list_name)->first();
      $customer = Customer::find($customer_id);
      if (!is_null($customer)){
        if ($customer->list_id == $list->id ) {
          $customer->status = 0;
          $customer->save();
        }
      }
			$message = "";
			if (!is_null($list)){
				$message = $list->unsubs_custom_message;
				$message = str_replace("[LIST_NAME]",$list->label,$message);
			}

      // return redirect($list->name)->with('message',$message);
      return view('layouts.unsubscribe',['label'=>$list->label]);
    }

    public function Country(Request $request)
    {
        $search = $request->search;
        $result = str_replace("+", "", $search);

        $countries = Countries::where('name','LIKE','%'.$search.'%')->orWhere('code','=',$result)->orderBy('name','asc')->get();
        return view('countries',['data'=>$countries]);
    }
    
    
    /************************ OLD CODES ************************/

    //Reminder
    public function index(Request $request, $product_list){
    	$check_link = UserList::where([
            ['name','=',$product_list],
            ['is_event','=',0],
            ['status','=',1],
        ])->first();

    	if(empty($product_list)){
    		return redirect('/');
    	} elseif(is_null($check_link)) {
    		return redirect('/');
    	} else {
            $list = UserList::where('name',$product_list)->first();
            $additional = Additional::where('list_id',$list->id)->get();
            $data = array();
            $arr = array();
            $data['fields'] = array();

            if($additional->count() > 0)
            {
                foreach($additional as $row)
                {
                   if($row->id_parent == 0){
                        $data['fields'][] = $row;
                   } 
                }
            }

            if(count($data['fields']) > 0)
            {
                foreach($data['fields'] as $col)
                {
                     // count if name has child or not
                     $doption = Additional::where([['list_id',$list->id],['id_parent',$col->id]])->get();

                     if($doption->count() > 0)
                     {
                         foreach($doption as $rows)
                         {
                            $arr[$col->name][$col->is_field][] = $rows->name;
                         }
                     } 
                     else 
                     {
                            $arr[$col->name][$col->is_field] = $col;
                     }
                } 

            }
            
    		return view('register-customer',['id'=>encrypt($list->id),'content'=>$list->content,'listname'=>$product_list,'pixel'=>$list->pixel_text,'message'=>$list->message_text,'additional'=>$arr]);
    	}
    }

    //Event
    public function event(Request $request, $product_list){
        $check_link = UserList::where([
            ['name','=',$product_list],
            ['is_event','=',1],
            ['status','=',1],
        ])->first();

        if(empty($product_list)){
            return redirect('/');
        } elseif(is_null($check_link)) {
            return redirect('/');
        } else {
            $list = UserList::where('name',$product_list)->first();
            $additional = Additional::where('list_id',$list->id)->get();
            $data = array();
            $arr = array();
            $data['fields'] = array();

            if($additional->count() > 0)
            {
                foreach($additional as $row)
                {
                   if($row->id_parent == 0){
                        $data['fields'][] = $row;
                   } 
                }
            }

            if(count($data['fields']) > 0)
            {
                foreach($data['fields'] as $col)
                {
                     # count if name has child or not
                     $doption = Additional::where([['list_id',$list->id],['id_parent',$col->id]])->get();

                     if($doption->count() > 0)
                     {
                         foreach($doption as $rows)
                         {
                            $arr[$col->name][$col->is_field][] = $rows->name;
                         }
                     } 
                     else 
                     {
                            $arr[$col->name][$col->is_field] = $col;
                     }
                } 

            }
            return view('register-customer',['id'=>encrypt($list->id),'content'=>$list->content, 'listname'=>$product_list,'pixel'=>$list->pixel_text,'message'=>$list->message_text,'additional'=>$arr]);
        }
    }

    public function addCustomer(Request $request)
    {
        $listname = $request->listname;
        $req = $request->all();

        $get_id_list = UserList::where('name','=',$listname)->first();
        $wa_number = '+62'.$request->wa_number;
        $today = Carbon::now();
        $wassenger = null;
        $evautoreply = false;
        $valid_customer = false;
        $is_event = $get_id_list->is_event;
        //message & pixel
        $list_message = $get_id_list->message_text;
        $list_wa_number = $get_id_list->wa_number;

        if(isset($req['data']))
        {
            $addt = json_encode($req['data']);
        } 
        else {
            $addt = null;
        }

        // Filter to avoid unavailable link 
        if(is_null($get_id_list)){
            return redirect('/');
        } 
        else {
            $customer = new Customer;
            $customer->user_id = $get_id_list->user_id;
            $customer->list_id = $get_id_list->id;
            $customer->name = $request->name;
            $customer->wa_number = $wa_number;
            $customer->additional = $addt;
            $customer->save();
            $customer_subscribe_date = $customer->created_at;
            $customerid = $customer->id;
        }

        // if customer successful sign up 
        if($customer->save() == true){
          $valid_customer = true;
        } 
        else {
          $data['success'] = false;
          $data['message'] = 'Error-000! Sorry there is something wrong with our system';
          return response()->json($data);
        }

        if($is_event == 1 && $valid_customer == true){
            // Event
            $reminder = Reminder::where([
                ['reminders.list_id','=',$get_id_list->id],
                ['lists.is_event','=',1],
                ['reminders.hour_time','<>',null],
                ['reminders.status','=',1],
                ])
                ->leftJoin('lists','reminders.list_id','=','lists.id')
                ->select('reminders.*','lists.event_date')
                ->get();
        } 
        else if($is_event == 0 && $valid_customer == true) {
            // Reminder
             $reminder = Reminder::where([
                ['reminders.list_id','=',$get_id_list->id],
                ['lists.is_event','=',0],
                ['reminders.days','>',0],
                ['reminders.hour_time','=',null],
                ['reminders.status','>',0],
                ])
                ->join('lists','reminders.list_id','=','lists.id')
                ->select('reminders.*')
                ->get(); 
        }
        
        if($reminder->count() > 0 && $is_event == 1)
        {
           //Event
            foreach($reminder as $row)
            {
              $today_event = Carbon::now()->toDateString();
              $days = (int)$row->days;
              $event_date = Carbon::parse($row->event_date);

              if($days < 0){
                $days = abs($days);
                $event_date->subDays($days);
              } 
              else {
                $event_date->addDays($days);
              }

              if($event_date >= $today_event){
                  $reminder_customer = new ReminderCustomers;
                  $reminder_customer->user_id = $row->user_id;
                  $reminder_customer->list_id = $row->list_id;
                  $reminder_customer->reminder_id = $row->id;
                  $reminder_customer->customer_id = $customerid;
                  $reminder_customer->save();
                  $eligible = true;
              } 
              else {
                $eligible = null;
              }

            }

            if($eligible == true){
                //return $this->autoReply($get_id_list->id,$wa_number,$list_message,$list_wa_number,$request->name);
                $data['success'] = true;
                $data['message'] = 'Thank you for join us';
                return response()->json($data);
            } 
            else if($eligible == null) {
                $data['message'] = 'Sorry this event has expired';
                return response()->json($data);
            } 
            else {
                $data['success'] = false;
                $data['message'] = 'Error-001! Sorry there is something wrong with our system';
                return response()->json($data);
            }
        } 
        else if($reminder->count() > 0 && $is_event == 0) 
        {
            // Reminder
            foreach($reminder as $row)
            {
                $days = (int)$row->days;
                $after_sum_day = Carbon::parse($customer_subscribe_date)->addDays($days);
                $validday = $after_sum_day->toDateString();
                $createdreminder = Carbon::parse($row->created_at)->toDateString();

                if($validday >= $createdreminder){
                    $reminder_customer = new ReminderCustomers;
                    $reminder_customer->user_id = $row->user_id;
                    $reminder_customer->list_id = $row->list_id;
                    $reminder_customer->reminder_id = $row->id;
                    $reminder_customer->customer_id = $customerid;
                    $reminder_customer->save(); 
                    $eligible = true; 
                } else {
                    $eligible = null;
                }
            }

            if($is_event == 1){
                $msg = 'event';
            } 
            else {
                $msg = 'reminder';
            }

              // if reminder has been set up into reminder-customer 
            if($eligible == true){
                //return $this->autoReply($get_id_list->id,$wa_number,$list_message,$list_wa_number,$request->name);
                $data['success'] = true;
                $data['message'] = 'Thank you for join us';
            } else if($eligible == null) {
                $data['message'] = 'Sorry this '.$msg.' has expired';
                return response()->json($data);
            } else {
                $data['success'] = false;
                $data['message'] = 'Error-002! Sorry there is something wrong with our system';
                return response()->json($data);
            }    
        } else {
            $data['success'] = true;
            $data['message'] = 'Thank you for join us';
        }
    }

    public function autoReply($listid,$wa_number,$list_message,$list_wa_number,$customer_name)
    {
        // send wa link to send message to list owner
        $list_wa_device = $list_wa_number;
        $list_wa_number = str_replace("+","",$list_wa_number);
        $data['wa_link'] = 'whatsapp://send?phone='.$list_wa_number.'&text='.$list_message.'';
        // $data['wa_link'] = 'https://api.whatsapp.com/send?phone='.$list_wa_number.'&text='.$list_message.'';

         // Sending event auto reply for customer, return true if user has not set auto reply yet
        $autoreply = Reminder::where([
                ['reminders.list_id','=',$listid],
                ['reminders.days','=',0],
                ['reminders.hour_time','=',null],
                ['reminders.status','=',1],
                ])->select('reminders.*')->first();

        if(is_null($autoreply)){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } 
        else {
             // wassenger
            $user_id = $autoreply->user_id;
            $getsender = Sender::where([['user_id',$user_id],['wa_number','=',$list_wa_device]])->first();
        }

        if(is_null($getsender))
        {
            $data['success'] = false;
            $data['message'] = 'Error-Send! Sorry, looks like if owner of this event had not set WA number yet';
            return response()->json($data);
        }
        else
        {
            $deviceid = $getsender->device_id;
            $message = str_replace('{name}',$customer_name,$autoreply->message);
            $status = $autoreply->status;
        }

        if($status == 1){
            $sendmessage = new SendMessage;
            $wasengger = $sendmessage->sendWA($wa_number,$message,$deviceid);
        } 
        else {
            $wasengger = null;
        }

        // if status from reminder has set to 0 or disabled
        if($wasengger == null && $status > 1){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } 
        else if($wasengger !== null && $status == 1){
            $data['success'] = true;
            $data['message'] = 'Thank You For Join Us';
            return response()->json($data);
        } 
        else {
            $data['success'] = false;
            $data['message'] = 'Error-WAS! Sorry there is something wrong with our system';
            return response()->json($data);
        }
    }

/* end of class */
}
