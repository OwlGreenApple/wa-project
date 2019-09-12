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
use Carbon\Carbon;
use App\User;
use App\Sender;

class SendWA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:wa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send whatsapp message to customer according on broadcast or reminder customer';

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

    public function handle()
    {
        /* Users counter */
        $user = User::select('id','counter','api_key')->get();
        foreach($user as $userow){
            $id_user = $userow->id;
            $count = $userow->counter;
            $api_key = $userow->api_key;
            $broadcast_customers = BroadCastCustomers::where([
                ['broad_cast_customers.user_id','=',$id_user],
                ['broad_cast_customers.status','=',0],
            ])->leftJoin('customers','customers.id','=','broad_cast_customers.customer_id')
            ->select('customers.wa_number','broad_cast_customers.message','broad_cast_customers.id')
            ->orderBy('broad_cast_customers.id','asc');

            /* Broadcast */
            if($broadcast_customers->count() > 0){
                /* get user id where status = 0 asc */
                $broadcast = $broadcast_customers->take($count)->get();
                foreach($broadcast as $id){
                      /*... Wasennger function ...*/
                      $wa_number = $id->wa_number;
                      $message = $id->message;
                      /* Send WA */
                      $wasengger = $this->sendWA($wa_number,$api_key,$message);
                     
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

                      /* Update when has sent message */
                      if(!empty($wasengger)){
                        $update_broadcast = BroadCastCustomers::where('id',$id->id)->update(['id_wa'=>$wasengger->id,
                            'status'=>$status,
                        ]);
                      } else {
                            echo 'Error!! Unable to send WA to customer';
                      }

                      if($update_broadcast == true){
                            // cut user's wa bandwith
                            $count = $count - 1;
                            User::where('id',$id_user)->update(['counter'=>$count]);
                      } else {
                            echo 'Error!! Unable to update broadcast customer';
                      }
                }
            } else {
            /* Reminder */
                $current_time = Carbon::now();
                $reminder_customers = ReminderCustomers::where([
                    ['user_id','=',$id_user],
                    ['status','=',0],
                ])->orderBy('id','asc');

                $check_event = Reminder::where([
                  ['user_id',$id_user],
                  ['days','<>',0],
                  ['status',1]
                ]);

                /* if event available */
                if($check_event->count() > 0){
                   return $this->dateEvent();
                } 

               /* get days from reminder */
                $reminder = Reminder::where('reminders.user_id','=',$id_user)
                                ->rightJoin('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
                                ->where('reminder_customers.status','=',0)
                                ->rightJoin('customers','customers.id','=','reminder_customers.customer_id')
                                ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.days','reminders.created_at as datecr','customers.created_at AS cstreg',
                                  'customers.wa_number','reminder_customers.message')
                                ->take($count)
                                ->get();

                /* check date reminder customer and update if succesful sending */
                foreach($reminder as $col) {
                    $date_reminder = Carbon::parse($col->datecr); //date when reminder was created
                    $day_reminder = $col->days; // how many days
                    $customer_signup = Carbon::parse($col->cstreg);
                    $adding = $customer_signup->addDays($day_reminder);
                    $reminder_customer_status = $col->rc_st;
                    $reminder_customers_id = $col->rcs_id;
                    $wa_number = $col->wa_number;
                    $message = $col->message;
                    $is_event = $col->is_event;
                    $event_date = $col->event_date;
                    $wasengger = null;

                    /* if customer register after adding days >= date when reminder was created */
                    if(($adding >= $date_reminder)){
                       $sending = true;
                    } else {
                       $sending = false;
                    }

                    $wasengger = $this->sendWA($wa_number,$api_key,$message);
                    /* if the time has reach or pass added time */
                    if(($sending == true) && ($current_time >= $adding) && $reminder_customer_status == 0){
                         /* wasengger */
                         $wasengger;
                    }

                    /* Determine status on reminder-customer */
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

                    if(!empty($wasengger)){
                         $update_reminder_customer = ReminderCustomers::where([
                            ['id',$reminder_customers_id],
                            ['status','=',0],
                        ])->update([
                            'id_wa'=>$wasengger->id,
                            'status'=>$status,
                        ]);
                    } else {
                        continue;
                    }

                    if($update_reminder_customer == true){
                         // cut user's wa bandwith
                        $count = $count - 1;
                        $user_update = User::where('id',$id_user)->update(['counter'=>$count]);
                    } else {
                        echo 'Error!! Unable to update reminder customer';
                    }

                    if($user_update == false){
                        echo 'Error!! Unable to update user counter';
                    }
                }
            }

        /* end user looping */
        }

    /* End function handle */    
    }

    public function sendWA($wa_number,$api_key,$message){
        $curl = curl_init();

        $data = array(
            'phone'=>$wa_number,
            'message'=>$message
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wassenger.com/v1/messages",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($data,true),
          CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "token: ".$api_key.""
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          //echo $response."\n";
            return json_decode($response);
        }
    }

    /* Event date */
    public function dateEvent(){
      $sender = Sender::select('id','counter','api_key','user_id')->get();

      foreach($sender as $rowuser){
          $id_user = $rowuser->user_id;
          $count = $rowuser->counter;
          $api_key = $rowuser->api_key;
          $idr = null;
          $wasengger = null;
          $event = null;

          $reminder = Reminder::where([
                  ['user_id',$id_user],
                  ['days','<>',0],
                  ['status',1]
          ])->get();

          /* reminder */

          if($reminder->count() > 0){
               foreach($reminder as $rows){
                $id_reminder = $rows->id;
                $today = Carbon::now();
                $event_date = Carbon::parse($rows->event_date);
                $days = (int)$rows->days;
                //hour according user set it to send WA
                $hour = $rows->hour_time.':00';
                //$hour = date('H:m:s',strtotime($rows->hour_time));

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

          if(!empty($idr) || $idr !== null)
          {
              foreach($idr as $id_reminder){
               //echo $id_reminder."\n";
                // to ge customer wa number and message 
                $remindercustomer = ReminderCustomers::where([
                      ['reminder_customers.user_id','=',$id_user],
                      ['reminder_customers.reminder_id','=',$id_reminder],
                      ['reminder_customers.status','=',0],
                ])->join('customers','customers.id','=','reminder_customers.customer_id')->select('customers.wa_number','reminder_customers.message','reminder_customers.id AS rc_id','customers.id AS cs_id','reminder_customers.reminder_id AS id_reminder')->get();
                

                foreach($remindercustomer as $col){
                    $event[] = $col;
                }
              } # end foreach reminder 
          }
          

          /* limit data according on count */
          if(!empty($event)){
             $event = array_slice($event,0,$count);
          }
          

          /* update according on reminder customer */
          if(!empty($event) || $event !== null){
             foreach($event as $col)
              {
                $wa_number = $col->wa_number;
                $message = $col->message;
                $id_reminder = $col->id_reminder;

                $wasengger = $this->sendWA($wa_number,$api_key,$message);

                if(!empty($wasengger)){
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
                    
                    $update = ReminderCustomers::where([
                      ['user_id','=',$id_user],
                      ['id','=',$col->rc_id],
                      ['status','=',0],
                    ])->update([
                      'status'=>$status,
                      'id_wa'=>$wasengger->id
                    ]);
                } else {
                    break;
                }

                $checkuser = ReminderCustomers::where('user_id',$id_user)->get();
               
                if($update == true && $checkuser->count() > 0){
                   $count = $count - 1;
                   $user_update = User::where('id',$id_user)->update(['counter'=>$count]);
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

    }

/* End command class */    
}