<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use App\Customer;
use App\Helpers\Spintax;
use Carbon\Carbon;
use App\User;
use App\PhoneNumber;
use DB;

class SendTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:telegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send telegram message to customer according on broadcast or reminder customer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
         // Users counter 
       /* $user = User::select('id')->orderBy('id','asc')->get();
        $wasengger = null;
        $current_time = Carbon::now();
        $userid = array();
        if($user->count() > 0){
          foreach($user as $row){
              $userid[] = $row->id;
          }
        }*/

       /* return $this->dateEvent($userid); 
        die('');
        return $this->dateReminder($userid);*/
        
        $broadcast = BroadCast::select("broad_casts.*","broad_cast_customers.*","broad_cast_customers.id AS bccsid","phone_numbers.id AS phoneid","users.id")
          ->join('users','broad_casts.user_id','=','users.id')
          ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')
          ->join('phone_numbers','phone_numbers.user_id','=','broad_casts.user_id')
          ->orderBy('broad_casts.user_id')
          ->get();

        //$check_event = ReminderCustomers::whereIn('user_id',$userid)->get();
               
        if($broadcast->count() > 0)
        {
            $number = 0;
            foreach($broadcast as $row)
            {
                
                $customers = Customer::where('id',$row->customer_id)->first();
                $message = $row->message;
                $phoneNumber = PhoneNumber::find($row->phoneid);

                if(!is_null($customers))
                {
                    $message = str_replace('{name}',$customers->name,$row->message);
                    $chat_id = $customers->chat_id;  
                    $counter = $phoneNumber->counter;

                    if($counter <= 0) {
                        continue;
                    }

                    if($counter > 0)
                    {
                        $campaign = 'broadcast';
                        $id_campaign = $row->bccsid;
                        $status = 'Sent';
                        $number ++;
                        $this->sendTelegram($phoneNumber,$message);
                        // $this->generateLog($number,$campaign,$id_campaign,$status);

                        $phoneNumber->counter --;
                        $phoneNumber->save();
                    }
                    else {
                        $campaign = 'broadcast';
                        $id_campaign = $row->bccsid;
                        $status = 'Error';
                        $number ++;
                        $this->generateLog($number,$campaign,$id_campaign,$status);
                    }
                   
                  /*try{                   
                      $wasengger = $this->sendTelegram($phoneNumber,$chat_id,$message);
                      $campaign = 'broadcast';
                      $id_campaign = $rows->bccsid;
                      $status = 'Sent';
                      $this->generateLog($number,$campaign,$id_campaign,$status);
                  } catch(Exception $e){
                      //echo $e->getMessage();
                      $campaign = 'broadcast';
                      $id_campaign = null;
                      $status = 'Error';
                      $this->generateLog($number,$campaign,$id_campaign,$status);
                  }
                  */
                }
                
            }//END LOOPING

        } // END BROADCAST AND THEN EVENT
        else if($check_event->count() > 0)
        {
            //EVENT 
            return $this->dateEvent($userid);
        }

        die('');
        if($user->count() > 0){
          foreach($user as $row){
            $id_user = $row->id;
            $phoneNumber = PhoneNumber::
                            where([['user_id','=',$id_user]])
                            ->select('counter')
                            ->first();
            if(!is_null($phoneNumber)){
              $count = $phoneNumber->counter;
            }
            
            $check_event = ReminderCustomers::
            where([
              ['reminder_customers.user_id',$id_user],
              ['reminder_customers.status',0],
              ['reminders.is_event','=',1],
            ])->join('reminders','reminder_customers.reminder_id','=','reminders.id')
            ->select('reminder_customers.*')
            ->get();

            $broadcast_customers = BroadCastCustomers::where([
                ['broad_casts.user_id','=',$id_user],
                ['broad_cast_customers.status','=',0],
            ])->get();
           /* ->leftJoin('broad_casts','broad_casts.id','=','broad_cast_customers.broadcast_id')
            ->rightJoin('customers','customers.id','=','broad_cast_customers.customer_id')
            ->select('customers.chat_id','customers.name','broad_casts.message','broad_cast_customers.id')
            ->orderBy('broad_cast_customers.id','asc')*/

            /* Broadcast */
            if($broadcast_customers->count() > 0){
                /* get user id where status = 0 asc */
                //$broadcast = $broadcast_customers->take($count)->get();
                foreach($broadcast_customers as $id){
                      $message = str_replace('{name}',$id->name,$id->message);

                      /*
                      */
                      try
                      {
                        $wasengger = $this->sendTelegram($phoneNumber,$message);
                      }catch(Exception $e){
                        //echo $e->getMessage();
                      }
                     
                    /* if($wasengger !== null && $wasengger->status == 'queued')
                     {
                          Determine status on BroadCast-customer 
                          $delivery_status = $wasengger->deliveryStatus;
                          if($delivery_status == 'queued'){
                            $status = 1;
                          } elseif($delivery_status == 'sent') {
                            $status = 2;
                          } elseif($delivery_status == 'failed') {
                            $status = 5;
                          } else {
                            $status = 0;
                          }


                        $update_broadcast = BroadCastCustomers::where('id',$id->id)->update([
                            'status'=>$status,
                        ]);
                     } else {
                         echo 'Telegram error code : '.$wasengger->status;
                         break;  
                     }
                    */

                     /*
                      if($update_broadcast == true){
                            // cut user's wa bandwith
                            $device_id = $wasengger->device;
                            $count = $count - 1;
                            PhoneNumber::where(['user_id',$id_user])->update(['counter'=>$count]);
                      } else {
                            echo 'Error!! Unable to update broadcast customer';
                      }
                      */
                }
            } 
            else if($check_event->count() > 0){
              //Event 
              //return $this->dateEvent($id_user);
            } 
            else 
            {
              // return $this->dateReminder($id_user);
            }

          /* end user looping */
          }
        } /* end user if */

      /* End function handle */
    }    
 
    public function sendTelegram($phoneNumber,$chat_id,$message)
    {
      $curl = curl_init();
      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phoneNumber->phone_number,
          // 'username'=>"gungunomni", 
          'chat_id'=>$chat_id, 
          'message'=>$message, 
          'filename'=>$phoneNumber->filename,
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/sendMessage.php",
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_POST => 1,
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        echo $response."\n";
        // print_r($response);
        // return json_decode($response, true);
      }
      
    }

    /* EVENT */
    public function dateEvent($user_id)
    {
          $idr = null;
          $event = null;

          $reminder = ReminderCustomers::whereIn('reminder_customers.user_id',$user_id)->where([
                  ['reminder_customers.status',0], //1 => active
                  ['reminders.is_event',1], //1 => active
          ])
          ->join('reminders','reminder_customers.reminder_id','=','reminders.id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.*')
          ->get();

          if($reminder->count() > 0){

              $number = 0;
              foreach($reminder as $rows)
              {
                $id_reminder = $rows->id;
                $today = Carbon::now();
                $event_date = Carbon::parse($rows->event_date);
                $days = (int)$rows->days;
                //hour according user set it to send WA
                $hour = $rows->hour_time;
                //$hour = date('H:m:s',strtotime($rows->hour_time));

                $phoneNumber = PhoneNumber::where('user_id','=',$rows->user_id)->select('counter')->first();

                if(!is_null($phoneNumber)){
                  $count = $phoneNumber->counter;
                }

                // if the day before / substract 
                if($days < 0){
                  $days = abs($days);
                  $event_date->subDays($days);
                } else {
                  $event_date->addDays($days);
                }

                $time_sending = $event_date->toDateString().' '.$hour;
                // get id reminder for reminder customer
                if($today >= $time_sending){
                    $number++;
                    $campaign = 'Event';
                    $id_campaign = $row->bccsid;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);

                    $count--;
                    PhoneNumber::where([['user_id',$rows->user_id]])->update(['counter'=>$count]);
                }

               /*  // limit data according on count 
                if(count($idr) > 0){
                   $idr = array_slice($idr,0,$count);
                }
                else
                {
                  return $this->dateReminder();
                }*/
              
              }//end for loop event
          }

          die('');
          if($reminder->count() > 0){

              foreach($reminder as $rows)
              {
                $id_reminder = $rows->id;
                $today = Carbon::now();
                $event_date = Carbon::parse($rows->event_date);
                $days = (int)$rows->days;
                //hour according user set it to send WA
                $hour = $rows->hour_time;
                //$hour = date('H:m:s',strtotime($rows->hour_time));

                $phoneNumber = PhoneNumber::where('user_id','=',$rows->user_id)->select('counter')->first();

                if(!is_null($phoneNumber)){
                  $count = $phoneNumber->counter;
                }

                // if the day before / substract 
                if($days < 0){
                  $days = abs($days);
                  $event_date->subDays($days);
                } else {
                  $event_date->addDays($days);
                }

                $time_sending = $event_date->toDateString().' '.$hour;
                // get id reminder for reminder customer
                if($today >= $time_sending){
                    $idr[] = $id_reminder;
                }

                 // limit data according on count 
                if(count($idr) > 0){
                   $idr = array_slice($idr,0,$count);
                }
                else
                {
                  return $this->dateReminder();
                }
              
              }//end for loop event
          }

          if(count($idr) > 0)
          {
              foreach($idr as $id_reminder){
               
                // to ge customer wa number and message 
                $remindercustomer = ReminderCustomers::where([
                            ['reminder_customers.user_id','=',$rows->user_id],
                            ['reminder_customers.reminder_id','=',$id_reminder],
                            ['reminder_customers.status','=',0],
                      ])->join('customers','customers.id','=','reminder_customers.customer_id')->join('reminders','reminders.id','=','reminder_customers.reminder_id')->select('customers.chat_id','customers.name','reminders.message','reminder_customers.id AS rc_id','customers.id AS cs_id','reminder_customers.reminder_id AS id_reminder','reminder_customers.user_id AS reminder_user_id')->first();
                
                $event[] = $remindercustomer;
              } // end foreach reminder 
          }

          // update according on reminder customer 
          if(count($event) > 0)
          {
            $number = 0;
            foreach($event as $col)
            {
                $message = str_replace('{name}',$col->name,$col->message);
                $id_reminder = $col->id_reminder;
                $chat_id = $col->chat_id;
                $number++;
                
                try
                {
                    $wasenggerevent = $this->sendTelegram($phoneNumber,$chat_id,$message);
                    $campaign = 'Event';
                    $id_campaign = $col->rc_id;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);
                }catch(Exception $e){
                    echo $e->getMessage();
                    $wasenggerevent = null;
                }

                $count = $count - 1;
                PhoneNumber::where([['user_id',$col->reminder_user_id]])->update(['counter'=>$count]);

                /*
                if($wasenggerevent !== null && $wasenggerevent->status == 'queued'){
                    $delivery_status = $wasenggerevent->deliveryStatus;
                    if($delivery_status == 'queued'){
                      $status = 1;
                    } elseif($delivery_status == 'sent') {
                      $status = 2;
                    } elseif($delivery_status == 'failed') {
                      $status = 5;
                    } else {
                      $status = 0;
                    } 
                    
                    $update = ReminderCustomers::where([
                      ['user_id','=',$user_id],
                      ['id','=',$col->rc_id],
                      ['status','=',0],
                    ])->update([
                      'status'=>$status,
                      'id_wa'=>$wasenggerevent->id
                    ]);
                } else {
                    echo 'Cannot send event message ';
                    break;
                }
                */

                /*
                $checkuser = ReminderCustomers::where('user_id',$user_id)->get();
               
                if($update == true && $checkuser->count() > 0){
                   $deviceId = $wasenggerevent->device;
                   $count = $count - 1;
                   // $user_update = Sender::where([['user_id',$user_id],['device_id','=',$deviceId]])->update(['counter'=>$count]);
                   PhoneNumber::where(['user_id',$user_id])->update(['counter'=>$count]);
                } else if($update == false && $checkuser->count() > 0) {
                   echo 'Error!! unable to update';
                   break;
                } else if($update == false && $checkuser->count() == 0) {
                   echo 'Note : There is user has nothing to update';
                   break;
                }
                */
            }//END FOREACH
          } 
    }

    /* REMINDER */
    public function dateReminder($user_id)
    {
        $coupon_code = null;

        // Reminder 
        $current_time = Carbon::now();
        $reminder = ReminderCustomers::whereIn('reminder_customers.user_id',$user_id)->where([
            ['reminder_customers.status','=',0],
            ['reminders.is_event','=',0],
            ['customers.created_at','<=',$current_time->toDateTimeString()],
            ])
            ->whereRaw('DATEDIFF(now(),customers.created_at) >= reminders.days')
            ->rightJoin('reminders','reminder_customers.reminder_id','=','reminders.id')
            ->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.days','reminders.message','customers.created_at AS cstreg','customers.chat_id','customers.name','reminders.id AS rid','reminders.user_id AS userid')
          //->take($count)
          ->get();

          //dd($reminder);

        $number = 0;
        foreach($reminder as $col) 
        {
            $phoneNumber = PhoneNumber::where('user_id','=',$col->userid)->select('counter')->first();
        
            if(!is_null($phoneNumber)){
              $count = $phoneNumber->counter;
            }

            $day_reminder = $col->days; // how many days
            $customer_signup = Carbon::parse($col->cstreg);
            $adding = $customer_signup->addDays($day_reminder);
            $reminder_customer_status = $col->rc_st;
            $reminder_customers_id = $col->rcs_id;
            //$event_date = $col->event_date;
            $message = $col->message;
            $chat_id = $col->chat_id;
            //$message = $col->message;
            $package = $col->package;
            $number++;

            if(($current_time >= $adding) && $reminder_customer_status == 0)
            {
                $wareminder = $this->sendTelegram($phoneNumber,$chat_id,$message);
                $campaign = 'Auto Responder';
                $id_campaign = $col->rid;
                $status = 'Sent';
                $this->generateLog($number,$campaign,$id_campaign,$status);
                $status = 1;
            }
            else 
            {
                $status = 0;
            }

        } #end reminder looping

        die('');
        /////////////////////////////////////////////////////////////
        // get days from reminder
        $reminder = ReminderCustomers::where([
                      ['reminder_customers.user_id','=',$user_id],
                      ['reminder_customers.status','=',0],
                      ['lists.is_event','=',0],
                      ['customers.created_at','<=',$current_time->toDateTimeString()],
                      ])
                      ->whereRaw('DATEDIFF(now(),customers.created_at) >= reminders.days')
                      ->rightJoin('reminders','reminder_customers.reminder_id','=','reminders.id')
                      ->join('lists','lists.id','=','reminders.list_id')
                      ->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
                      ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.package','reminders.days','reminders.message','customers.created_at AS cstreg','customers.chat_id','customers.name')
                    //->take($count)
                    ->get();

        /* check date reminder customer and update if succesful sending */
        foreach($reminder as $col) 
        {
            $day_reminder = $col->days; // how many days
            $customer_signup = Carbon::parse($col->cstreg);
            $adding = $customer_signup->addDays($day_reminder);
            $reminder_customer_status = $col->rc_st;
            $reminder_customers_id = $col->rcs_id;
            //$event_date = $col->event_date;
            $message = $col->message;
            $chat_id = $col->chat_id;
            //$message = $col->message;
            $package = $col->package;

            if(($current_time >= $adding) && $reminder_customer_status == 0)
            {
                $wareminder = $this->sendTelegram($phoneNumber,$chat_id,$message);
                $status = 1;
            }
            else 
            {
                $status = 0;
            }

            $update_reminder_customer = ReminderCustomers::where([
                ['id',$reminder_customers_id],
                ['status','=',0],
            ])->update([
                'id_wa'=>0,
                'status'=>$status,
            ]); 
        } #end reminder looping
    }

    public function generateLog($number,$campaign,$id_campaign,$error = null)
    {
        $timegenerate = Carbon::now();
        $logexists = Storage::disk('local')->exists('log/log.txt');
        $format = "No : ".$number.", Date and time : ".$timegenerate.", Type : ".$campaign.", id : ".$id_campaign.", Status : ".$error."\n";

        if($logexists == true)
        {
            $log = Storage::get('log/log.txt');
            $string = $log."\n".$format;
            Storage::put('log/log.txt',$string);
        }
        else
        {
            $string = $format;
            Storage::put('log/log.txt',$string);
        }
       
    }


/* End command class */    
}