<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use App\Customer;
use App\AdminSetting;
use Carbon\Carbon;
use DB;

class WABox extends Command
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
    protected $description = 'Test to send message to send telegram';

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
		//$this->testMessage();
		//die('');
        $broadcast = BroadCast::where('status','=',0)->get();

        if($broadcast->count() > 0)
        {
           $this->broadcastMessage();
        }
        else
        {
           $this->reminderMessage();
        }
    }

    #BROADCAST MESSAGE TELEGRAM
    public function broadcastMessage()
    {
      $broadcast = BroadCast::where('broad_casts.status','=',0)
                  ->rightJoin('lists','lists.id','=','broad_casts.list_id')
                  ->select('lists.bot_api','broad_casts.id as bid','broad_casts.message')
                  ->get();
      $broadcastidlist = array();
      $total_sending = 0;

      if($broadcast->count() > 0)
      {
          foreach($broadcast as $rows)
          {
				$broadcastmessage = $rows->message;
				$broadcastid = $rows->bid;
				$botapi = $rows->bot_api;
				$bcd = $this->getListBot($botapi,$broadcastmessage,$broadcastid);

				if(is_array($bcd))
				{
					$broadcastidlist[$broadcastid][] = $bcd;
				}
          }
      }
	 
      if(count($broadcastidlist) > 0)
      {
         foreach($broadcastidlist as $broadcastid=>$broadcasts)
         {
            foreach($broadcasts as $col)
            {
              foreach($col as $brc)
              {
                  $broadcastcustomer = new BroadCastCustomers;
                  $broadcastcustomer->bot_api = $brc['bot_id'];
                  $broadcastcustomer->broadcast_id = $brc['idservice'];
                  $broadcastcustomer->message = $brc['message'];
                  $broadcastcustomer->chat_id = $brc['chat_id'];
                  $broadcastcustomer->save();
              }
            }
            BroadCast::where('id','=',$broadcastid)->update(['status'=>1]);
            //print(print_r($broadcastid,true))."\n";
         }
      }

      $broadcastcustomer = BroadCastCustomers::where('status','=',0)->get();
      $broadcast_customer_count = $broadcastcustomer->count();
      if($broadcastcustomer->count() > 0)
      {
         foreach($broadcastcustomer as $col)
         {
            $id = $col->id;
            $this->sendMessage($col->chat_id,$col->message,$col->bot_api);
            $broadcast_customer_count--;
            $total_sending++;
            BroadCastCustomers::where('id','=',$id)->update(['status'=>1]);
            //print(print_r($broadcast_customer_count,true))."\n";

            if($broadcast_customer_count == 0){
                exit();
            }
            else{
                $this->getRandomSendAndDelay($total_sending,1);
            }
            
         }
      } 
    }

    #REMINDER MESSAGE TELEGRAM
    public function reminderMessage()
    {
      $current_time = Carbon::now();
      $reminder_list = null;
      $remindertel = array();
      $total_sending = 0;
      #REMINDER
      $reminder = Reminder::where([
                  ['reminders.status','=',1],
                  ['lists.is_event','=',0],
                  ['lists.status','=',1],
                  ['customers.created_at','<=',$current_time->toDateTimeString()]
                  ])
                  ->whereRaw("DATEDIFF(now(),customers.created_at) >= reminders.days")
                  ->join('lists','lists.id','=','reminders.list_id')
                  ->rightJoin('customers','customers.list_id','=','lists.id')
                  ->select('reminders.days','reminders.id AS rid','reminders.status AS rst','reminders.message','customers.created_at AS crt','lists.bot_api','customers.list_id AS clid')
                  ->get();

      if($reminder->count() > 0)
      {
        foreach($reminder as $col)
        {
           $day_reminder = $col->days; // how many days
           $customer_signup = Carbon::parse($col->crt);
           $adding = $customer_signup->addDays($day_reminder);
           $reminder_status = $col->rst;
           $botapi = $col->bot_api;
           $remindermessage = $col->message;
           $reminder_id = $col->rid;

           if($adding >= $current_time && $reminder_status == 1)
           {
              $reminder_list = $this->getListBot($botapi,$remindermessage,$reminder_id);
				if(is_array($reminder_list))
				{
					$remindertel[$reminder_id][] = $reminder_list;
				}
           }
        }

        if(count($remindertel) > 0)
        {
            foreach($remindertel as $idreminder=>$wrapper)
            {
                foreach($wrapper as $reminders)
                {
                  foreach($reminders as $col)
                  {
                      $remindercustomer = new ReminderCustomers;
                      $remindercustomer->bot_api = $col['bot_id'];
                      $remindercustomer->reminder_id = $col['idservice'];
                      $remindercustomer->message = $col['message'];
                      $remindercustomer->chat_id = $col['chat_id'];
                      $remindercustomer->save();
                  }
                }
                Reminder::where('id','=',$idreminder)->update(['status'=>0]);
            }
        } 

          $reminder_customer = ReminderCustomers::where('status','=',0)->get();
          $reminder_customer_count = $reminder_customer->count();
          if($reminder_customer_count > 0)
          {
              foreach($reminder_customer as $col)
              {
                $id_reminder = $col->id;
                $this->sendMessage($col->chat_id,$col->message,$col->bot_api);
                $reminder_customer_count--;
                $total_sending++;
                ReminderCustomers::where('id','=',$id_reminder)->update(['status'=>1]);
                //print(print_r($reminder_customer_count,true))."\n";

                 if($reminder_customer_count == 0){
                      exit();
                  }
                  else{
                      $this->getRandomSendAndDelay($total_sending,2);
                  }
              }
                 
          }
        #END REMINDER
      }
      else 
      #EVENT
      {
         $idr = null;
         $event_list = $eligible = array();

         $event = Reminder::where([
                ['reminders.status','=',1],
                ['lists.is_event','=',1],
                ['lists.status','=',1]
                ])
                ->rightJoin('lists','lists.id','=','reminders.list_id')
                ->select('reminders.days','reminders.hour_time','reminders.id AS rid','reminders.message','reminders.subject','lists.bot_api','lists.event_date','lists.bot_api')
                ->get();

          if($event->count() > 0)
          {
              foreach($event as $rows)
              {
                  $event_date = Carbon::parse($rows->event_date);
                  $days = (int)$rows->days;
                  $hour = $rows->hour_time.':00';
                  $id_reminder = $rows->rid;
                  $event_message = $rows->message;
                  $botapi = $rows->bot_api;

                  if($days < 0){
                    $days = abs($days);
                    $event_date->subDays($days);
                  } else {
                    $event_date->addDays($days);
                  }

                  $time_sending = $event_date->toDateString().' '.$hour;
                  // get id reminder for reminder customer
                  if($current_time >= $time_sending)
                  {
                     $eligible[] = array(
                        'botapi'=>$botapi,
                        'message'=>$event_message,
                        'idservice'=>$id_reminder
                     );
                  }
              }
          }

          #COLLECT ELIGIBLE USER TO GET MESSAGE
          if(count($eligible) > 0)
          {
              foreach($eligible as $tab)
              {
                 $botapi = $tab['botapi'];
                 $event_message = $tab['message'];
                 $id_reminder_customer = $tab['idservice'];

                 $idr = $this->getListBot($botapi,$event_message,$id_reminder_customer);
				 if(is_array($idr))
				 {
					$event_list[$id_reminder_customer][] = $idr;
				 }
              }
          }

          if(count($event_list) > 0)
          {
              foreach($event_list as $idreminder=>$events)
              {
                  foreach($events as $wrap)
                  {
                    foreach($wrap as $col)
                    {
                        $remindercustomer = new ReminderCustomers;
						$remindercustomer->bot_api = $col['bot_id'];
						$remindercustomer->reminder_id = $col['idservice'];
						$remindercustomer->message = $col['message'];
						$remindercustomer->chat_id = $col['chat_id'];
						$remindercustomer->save();
                    }
                  }
                Reminder::where('id','=',$idreminder)->update(['status'=>0]);
              }
          }
		  
		  $reminder_customer = ReminderCustomers::where('status','=',0)->get();
          $reminder_customer_count = $reminder_customer->count();
          if($reminder_customer_count > 0)
          {
              foreach($reminder_customer as $col)
              {
                $id_reminder = $col->id;
                $this->sendMessage($col->chat_id,$col->message,$col->bot_api);
                $reminder_customer_count--;
                $total_sending++;
                ReminderCustomers::where('id','=',$id_reminder)->update(['status'=>1]);
                print(print_r($reminder_customer_count,true))."\n";

                 if($reminder_customer_count == 0){
                      exit();
                  }
                  else{
                      $this->getRandomSendAndDelay($total_sending,2);
                  }
              }
		  }
		  
	  #END IF	  
      }
    
    #END REMINDERMESSAGE  
    }

    public function getListBot($botapi,$message,$mid)
    {
      #$mid could be broadcast id, event, reminder
      $curl = curl_init();
      $data = $temp = $target = array();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.telegram.org/bot".$botapi."/getUpdates?offset=0",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET"
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        $response = json_decode($response,true);
		
        if(count($response['result']) > 0)
        {
            foreach($response['result'] as $rows)
            {
				$temp[] = $rows['message'];
            }
        }
      }
	
      if(count($temp) > 0)
      {
        foreach($temp as $col)
        {
           $data[$botapi][] = $col['from']['id'];
        }
      }
      
      if(count($data) > 0)
      {
        foreach($data as $idbot=>$id)
        {
			  foreach(array_unique($id) as $row)
			  {
					//print(print_r($row,true))."\n";
					if(!empty($row))
					{
						$target[] = array(
						  'bot_id'=>$idbot,
						  'idservice'=>$mid, #id broadcast or reminder
						  'message'=>$message,
						  'chat_id'=>$row #id from telegram
						);
					}	
			  }
        }
       return $target;
      } 
    }

    public function getRandomSendAndDelay($total_sending,$idservice)
    {
        $settings = AdminSetting::where('id','=',1)->first();

        #IF IDSERVCE = 1 THEN BROADCAST, ELSE REMINDER
        if($idservice == 1)
        {
            $commands = $this->broadcastMessage();
        }
        else
        {
            $commands = $this->reminderMessage();
        }

        #TO CONTROL HOW MANY MINUTES MESSAGE SENT, AND DELAY TO SEND MESSAGE
        if(!is_null($settings))
        {
           $sending_start = $settings->total_message_start;
           $sending_end = $settings->total_message_end;
           $delay_start = $settings->delay_message_start;
           $delay_end = $settings->delay_message_end;

           $sending = mt_rand($sending_start,$sending_end);
           $delay = mt_rand($delay_start,$delay_end);
        }
        else {
           $sending = $delay = 1;
        }

        if($total_sending == $sending)
        {
            sleep($delay);
            return $commands;
        }
    }

    public function sendMessage($userid,$message,$botapi)
    {
        //$botapi = '973247472:AAF0NSv1sLEPNxOdRgS7vOPzxEr2odqNhb0';
        $curl = curl_init();
        $data = array(
          'chat_id'=>$userid,
          'text'=>$message,
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.telegram.org/bot".$botapi."/sendMessage",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err)
        {
          echo "cURL Error #:" . $err;
        }
    }

    public function testMessage()
    {
        $userid = '955127354';
        $message = 'testomni';
        $botapi = '938956757:AAGEQe9AbeOrXrTR6ZyLWTWdlbBYVanKapw';
        $curl = curl_init();
        $data = array(
          'chat_id'=>$userid,
          'text'=>$message,
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.telegram.org/bot".$botapi."/getUpdates?offset=0",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $data,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if($err)
        {
          echo "cURL Error #:" . $err;
        }
		else{
			dd($response);
		}

    }

/* end console */    
}
