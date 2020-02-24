<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    
    /* To test send wassenger

    public function handle()
    {

      $wa_number = '+628123238793';
      $api_key = '5fe578b72c10a69fdcbd5d629a183af1799610cef975338a865480a7e7ad29c5361eb07beaf80f16';
       for($x=1;$x<=10;$x++){
          $message = $x.'--- Test message sending 1 - 10';
          $this->sendWA($wa_number,$api_key,$message);
       }
     End function handle     
    }*/

    /*public function handle()
    {
      return $this->dateReminder();
    }*/

    public function handle()
    {
        /* Users counter */
        $user = User::select('id')->get();
        $wasengger = null;

        if($user->count() > 0){
          foreach($user as $userow){
            $id_user = $userow->id;
            $phoneNumber = PhoneNumber::where([['user_id','=',$id_user]])->select('counter')->first();
            if(!is_null($phoneNumber)){
              $count = $phoneNumber->counter;
            }
            
            $check_event = ReminderCustomers::where([
              ['reminder_customers.user_id',$id_user],
              ['reminder_customers.status',0],
              ['lists.is_event','=',1],
            ])->join('lists','reminder_customers.list_id','=','lists.id')
            ->select('reminder_customers.*')
            ->get();

            $broadcast_customers = BroadCastCustomers::where([
                ['broad_cast_customers.user_id','=',$id_user],
                ['broad_cast_customers.status','=',0],
            ])->leftJoin('customers','customers.id','=','broad_cast_customers.customer_id')
            ->select('customers.chat_id','customers.name','broad_cast_customers.message','broad_cast_customers.id')
            ->orderBy('broad_cast_customers.id','asc');

            /* Broadcast */
            if($broadcast_customers->count() > 0){
                /* get user id where status = 0 asc */
                $broadcast = $broadcast_customers->take($count)->get();
                foreach($broadcast as $id){
                      /*... Wasennger function ...*/
                      $chat_id = $id->chat_id;
                      $message = str_replace('{name}',$id->name,$id->message);

                      /*
                      */
                      
                      try
                      {
                        $wasengger = $this->sendTelegram($phoneNumber,$chat_id,$message);
                      }catch(Exception $e){
                        echo $e->getMessage();
                      }
                     
                     if($wasengger !== null && $wasengger->status == 'queued')
                     {
                         /* Determine status on BroadCast-customer */
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
                         echo 'Wassenger error code : '.$wasengger->status;
                         break;  
                     }
                    
                      if($update_broadcast == true){
                            // cut user's wa bandwith
                            $device_id = $wasengger->device;
                            $count = $count - 1;
                            PhoneNumber::where(['user_id',$id_user])->update(['counter'=>$count]);
                      } else {
                            echo 'Error!! Unable to update broadcast customer';
                      }
                }
            } else if($check_event->count() > 0){
              //Event 
              return $this->dateEvent();
            } 
            else 
            {
               return $this->dateReminder();
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
    public function dateEvent(){
      $user = User::select('id')->get();

      if($user->count() > 0){
        foreach($user as $rowuser){
          $id_user = $rowuser->id;
          $idr = null;
          $wasenggerevent = null;
          $event = null;

          $reminder = Reminder::where([
                  ['reminders.user_id',$id_user],
                  ['reminders.status',1],
                  ['lists.is_event',1],
          ])->join('lists','reminders.list_id','=','lists.id')
          ->select('reminders.*','lists.event_date')->get();

          /* event */
          if($reminder->count() > 0){
               foreach($reminder as $rows){
                $device_number = $rows->device_number;
                $id_reminder = $rows->id;
                $today = Carbon::now();
                $event_date = Carbon::parse($rows->event_date);
                $days = (int)$rows->days;
                //hour according user set it to send WA
                $hour = $rows->hour_time.':00';
                //$hour = date('H:m:s',strtotime($rows->hour_time));
                $phoneNumber = PhoneNumber::where([['user_id','=',$id_user]])->select('counter')->first();
                if(!is_null($phoneNumber)){
                  $count = $phoneNumber->counter;
                }

               
                /* if the day before / substract */
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
                
              //end for loop reminder
              }
          }

          if($idr !== null)
          {
              foreach($idr as $id_reminder){
               //echo $id_reminder."\n";
                // to ge customer wa number and message 
                $remindercustomer = ReminderCustomers::where([
                      ['reminder_customers.user_id','=',$id_user],
                      ['reminder_customers.reminder_id','=',$id_reminder],
                      ['reminder_customers.status','=',0],
                ])->join('customers','customers.id','=','reminder_customers.customer_id')->join('reminders','reminders.id','=','reminder_customers.reminder_id')->select('customers.chat_id','customers.name','reminders.message','reminder_customers.id AS rc_id','customers.id AS cs_id','reminder_customers.reminder_id AS id_reminder')->get();
                
                foreach($remindercustomer as $col){
                    $event[] = $col;
                }
              } # end foreach reminder 
          }

          
          /* limit data according on count */
          if($event !== null){
             $event = array_slice($event,0,$count);
          }
          else
          {
            return $this->dateReminder();
          }
          
          /* update according on reminder customer */
          if(!empty($event) || $event !== null){
            foreach($event as $col)
            {
                $message = str_replace('{name}',$col->name,$col->message);
                $id_reminder = $col->id_reminder;
                $chat_id = $col->chat_id;
                
                $phoneNumber = PhoneNumber::where([['user_id','=',$id_user]])->select('counter')->first();
                if(!is_null($phoneNumber)){
                  $count = $phoneNumber->counter;
                }


                try
                {
                    $wasenggerevent = $this->sendTelegram($phoneNumber,$chat_id,$message);
                }catch(Exception $e){
                    echo $e->getMessage();
                    $wasenggerevent = null;
                }

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
                      ['user_id','=',$id_user],
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

                $checkuser = ReminderCustomers::where('user_id',$id_user)->get();
               
                if($update == true && $checkuser->count() > 0){
                   $deviceId = $wasenggerevent->device;
                   $count = $count - 1;
                   // $user_update = Sender::where([['user_id',$id_user],['device_id','=',$deviceId]])->update(['counter'=>$count]);
                   PhoneNumber::where(['user_id',$id_user])->update(['counter'=>$count]);
                } else if($update == false && $checkuser->count() > 0) {
                   echo 'Error!! unable to update';
                   break;
                } else if($update == false && $checkuser->count() == 0) {
                   echo 'Note : There is user has nothing to update';
                   break;
                }

                //echo $col->rc_id."--".$wa_number."\n";
            }
          } 
  
          
        } /* end foreach user */

      } /* end if */

    }

    /* REMINDER */
    public function dateReminder()
    {
       /* Users counter */
        $user = User::select('id')->get();
        $coupon_code = null;
        if($user->count() > 0)
        {
          foreach($user as $userow)
          {
              $id_user = $userow->id;
              $phoneNumber = PhoneNumber::where([['user_id','=',$id_user]])->select('counter')->first();
              if(!is_null($phoneNumber)){
                $count = $phoneNumber->counter;
              }
             
              /* Reminder */
                $current_time = Carbon::now();
               /* get days from reminder */
                $reminder = ReminderCustomers::where([
                            ['reminder_customers.user_id','=',$id_user],
                            ['reminder_customers.status','=',0],
                            ['lists.is_event','=',0],
                            ['customers.created_at','<=',$current_time->toDateTimeString()],
                            ])
                            ->whereRaw('DATEDIFF(now(),customers.created_at) >= reminders.days')
                            ->rightJoin('reminders','reminder_customers.reminder_id','=','reminders.id')
                            ->join('lists','lists.id','=','reminders.list_id')
                            ->leftJoin('customers','customers.id','=','reminder_customers.customer_id')
                            ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.package','reminders.days','reminders.message','reminders.subject','reminders.mail','customers.created_at AS cstreg','customers.chat_id','customers.name','customers.email','customers.is_pay')
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
                    $customeremail = $col->email;
                    $package = $col->package;
                    $subject = $col->subject;
                    $mailmessage = $col->mail;
                    $is_pay = $col->is_pay;

                    // ??
                    $run = true;


                    if($is_pay == 0 && ($current_time >= $adding) && $reminder_customer_status == 0)
                    {
                        $wareminder = $this->sendTelegram($phoneNumber,$chat_id,$message);
                        $status = 1;
                    }
                    elseif($is_pay == 1)
                    {
                        $status = 4;
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
          /* end user looping */
          }
        /* end user if */  
        }
    }

/* End command class */    
}