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

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message message to customer according on broadcast or event or auto responder';

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
      //Broadcast 
      //$this->campaignBroadcast();
   
      //Auto Responder
      $this->campaignAutoResponder();
    }    
 
    public function sendMessage($phoneNumber = null,$message = null)
    {
      $phoneNumber = '+62895342472008';
      $key='d776f366b470d04d813e75e0e83623f032e6d1fa4a389de8'; //this is demo key please change with your own key
      $url='http://116.203.92.59/api/send_message';
      $data = array(
        "phone_no"=> $phoneNumber,
        "key"		=>$key,
        "message"	=>'Test haloooo'
      );
      $data_string = json_encode($data);

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_VERBOSE, 0);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
      curl_setopt($ch, CURLOPT_TIMEOUT, 360);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
      );
      echo $res=curl_exec($ch);
      curl_close($ch);
    }

    /* BROADCAST */
    public function campaignBroadcast()
    {
        $broadcast = BroadCast::select("broad_casts.*","broad_cast_customers.*","broad_cast_customers.id AS bccsid","phone_numbers.id AS phoneid","users.id")
          ->join('users','broad_casts.user_id','=','users.id')
          ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')
          ->join('phone_numbers','phone_numbers.user_id','=','broad_casts.user_id')
          ->where("broad_cast_customers.status",0)
          ->orderBy('broad_casts.user_id')
          ->get();

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
                        $this->sendMessage($phoneNumber,$message);
                        $this->generateLog($number,$campaign,$id_campaign,$status);

                        $phoneNumber->counter --;
                        $phoneNumber->save();
                        
                        $broadcastCustomer = BroadCastCustomers::find($row->bccsid);
                        if (!is_null($broadcastCustomer)){
                          $broadcastCustomer->status = 1;
                          $broadcastCustomer->save();
                        }
                    }
                    else {
                        $campaign = 'broadcast';
                        $id_campaign = $row->bccsid;
                        $status = 'Error';
                        $number ++;
                        $this->generateLog($number,$campaign,$id_campaign,$status);
                    }
                   
                  /*try{                   
                      $wasengger = $this->sendMessage($phoneNumber,$chat_id,$message);
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
                sleep(10);
            }//END LOOPING

        } // END BROADCAST 
    }

    /* EVENT */
    public function campaignEvent()
    {
          $idr = null;
          $event = null;
          $today = Carbon::now();

          $reminder = Reminder::where([
                  ['reminder_customers.status',0], 
                  ['reminders.is_event',1], 
          ])
          ->join('users','reminders.user_id','=','users.id')
          ->join('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.*','reminder_customers.id AS rcs_id','customers.name')
          ->get();

          if($reminder->count() > 0)
          {
              $number = $count = 0;
              foreach($reminder as $row)
              {
                $id_reminder = $row->id;
                $event_date = Carbon::parse($row->event_time);
                $days = (int)$row->days;
                $hour = $row->hour_time; //hour according user set it to sending

                $phoneNumber = PhoneNumber::where('user_id','=',$row->user_id)->select('counter','id')->first();

                if(!is_null($phoneNumber)){
                  $count = $phoneNumber->counter;
                }

                // if the day before / substract 
                if($days < 0){
                  $days = abs($days);
                  $date = $event_date->subDays($days);
                } else {
                  $date = $event_date->addDays($days);
                }

                $time_sending = $date->toDateString().' '.$hour;
                $deliver_time = Carbon::parse($time_sending)->diffInSeconds(Carbon::now(), false);

                // get id reminder for reminder customer
                if($deliver_time >= 0 && $count > 0){
                  $number++;
                  $campaign = 'Event';
                  $id_campaign = $row->rcs_id;
                  $status = 'Sent';

                  $message = str_replace('{name}',$row->name,$row->message);
                  $id_reminder = $row->id_reminder;
     
                  $this->sendMessage($phoneNumber,$message);
                  $this->generateLog($number,$campaign,$id_campaign,$status);

                  $remindercustomer_update = ReminderCustomers::find($id_campaign);
                  $remindercustomer_update->status = 1;
                  $remindercustomer_update->save();

                  $count--;
                  PhoneNumber::where([['id',$phoneNumber->id]])->update(['counter'=>$count]);
                }
                else
                {
                    $campaign = 'Event';
                    $id_campaign = $row->rcs_id;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);
                    continue;
                }
                sleep(10);
              }//END FOR LOOP EVENT
          }
    }

    /* AUTO RESPONDER */
    public function campaignAutoResponder()
    {
        // Reminder 
        $current_time = Carbon::now();
        $reminder = Reminder::where([
            ['reminder_customers.status','=',0],
            ['reminders.is_event','=',0],
            ['customers.created_at','<=',$current_time->toDateTimeString()],
            ])
            ->whereRaw('DATEDIFF(now(),customers.created_at) >= reminders.days')
            ->join('users','reminders.user_id','=','users.id')
            ->join('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.*','customers.created_at AS cstreg','customers.chat_id','customers.name','reminders.id AS rid','reminders.user_id AS userid')
          //->take($count)
          ->get();

        $number = $count = 0;
        $adding = null;

        if($reminder->count() > 0)
        {
            foreach($reminder as $col) 
            {
                $phoneNumber = PhoneNumber::where('user_id','=',$col->userid)->select('counter','id')->first();
            
                if(!is_null($phoneNumber)){
                  $count = $phoneNumber->counter;
                }

                $hour_time = $col->hour_time;
                $day_reminder = $col->days; // how many days
                $customer_signup = Carbon::parse($col->cstreg)->addDays($day_reminder);
                $adding_with_hour = $customer_signup->toDateString().' '.$hour_time;

                $reminder_customer_status = $col->rc_st;
                $reminder_customers_id = $col->rcs_id;

                $adding = Carbon::parse($adding_with_hour);         

                $message = $col->message;
                $chat_id = $col->chat_id;
                $message = $col->message;
                $number++;

                if($adding->lte(Carbon::now()) && $count > 0)
                {
                    $this->sendMessage($phoneNumber,$message);
                    $campaign = 'Auto Responder';
                    $id_campaign = 'reminder_customers_id = '.$col->rcs_id;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);

                    $remindercustomer_update = ReminderCustomers::find($reminder_customers_id);
                    $remindercustomer_update->status = 1;
                    $remindercustomer_update->save();

                    $count = $count - 1;
                    PhoneNumber::where('id',$phoneNumber->id)->update(['counter'=>$count]);
                }
                else 
                {
                    $campaign = 'Auto Responder';
                    $id_campaign = 'reminder_customers_id = '.$col->rcs_id;
                    $status = 'Not Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);
                    continue;
                }
                sleep(10);
            }//END LOOPING
        }
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