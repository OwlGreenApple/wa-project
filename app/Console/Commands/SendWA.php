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

               /* get days from reminder */
                $reminder = Reminder::where('reminders.user_id','=',$id_user)
                                ->rightJoin('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
                                ->where('reminder_customers.status','=',0)
                                ->rightJoin('customers','customers.id','=','reminder_customers.customer_id')
                                ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.days','reminders.created_at as datecr','customers.created_at AS cstreg','customers.wa_number','reminder_customers.message')
                                ->take($count)
                                ->get();

                /* check date reminder customer and update if succesful sending */
                foreach($reminder as $col) {
                    $date_reminder = Carbon::parse($col->datecr); //date when reminder was created
                    $day_reminder = $col->days; // how many days
                    $customer_signup = Carbon::parse($col->cstreg);
                    $adding = $customer_signup->addDays($day_reminder);
                    //$reminder_customer_status = $col->rc_st;
                    $reminder_customers_id = $col->rcs_id;
                    $wa_number = $col->wa_number;
                    $message = $col->message;
                    $wasengger = null;
                    /* if customer register after adding days >= date when reminder was created */

                    if(($adding >= $date_reminder)){
                       $sending = true;
                    } else {
                       $sending = false;
                    }

                    $wasengger = $this->sendWA($wa_number,$api_key,$message);
                    /* if the time has reach or pass added time */
                    if(($sending == true) && ($current_time >= $adding)){
                         /* wasengger */
                         $wasengger;
                    }

                    /* Determine status on BroadCast-customer */
                      $delivery_status = $wasengger->deliveryStatus;
                      if($delivery_status == 'queued'){
                        $status = 1;
                      } elseif($delivery_status == 'sent') {
                        $status = 2;
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
            'phone'=>'+'.$wa_number,
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

/* End command class */    
}
