<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\UserList;
use App\BroadCast;
use App\Reminder;
use App\ReminderCustomers;
use App\Customer;
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
        $botlist = UserList::where('status','=',1)->get();
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

      if($broadcast->count() > 0)
      {
          foreach($broadcast as $rows)
          {
            $broadcastmessage = $rows->message;
            $broadcastid = $rows->bid;
            $botapi = $rows->bot_api;

            $bcd = $this->getListBot($botapi,$broadcastmessage,$broadcastid);
            $broadcastidlist[] = $bcd;
          }
      }

      if(count($broadcastidlist) > 0)
      {
         foreach($broadcastidlist as $botid=>$broadcasts)
         {
            foreach($broadcasts as $col)
            {
               //print(print_r($col['bot_id'],true))."\n";
               $this->sendMessage($col['chat_id'],$col['message'],$col['bot_id']);
               BroadCast::where('id','=',$col['idservice'])->update(['status'=>1]);
               sleep(1);
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
              $remindertel[$reminder_id][] = $reminder_list;
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
                    //print(print_r($col,true))."\n";
                    $this->sendMessage($col['chat_id'],$col['message'],$col['bot_id']);
                    sleep(1);
                  }
                }
                Reminder::where('id','=',$idreminder)->update(['status'=>0]);
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

          #COLLECT ELIGIBLE USER FOR SENDING MESSAGE
          if(count($eligible) > 0)
          {
              foreach($eligible as $tab)
              {
                 $botapi = $tab['botapi'];
                 $event_message = $tab['message'];
                 $id_reminder_customer = $tab['idservice'];

                 $idr = $this->getListBot($botapi,$event_message,$id_reminder_customer);
                 $event_list[$id_reminder_customer][] = $idr;
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
                      //print(print_r($col,true))."\n";
                      $this->sendMessage($col['chat_id'],$col['message'],$col['bot_id']);
                      sleep(1);
                    }
                  }
                Reminder::where('id','=',$idreminder)->update(['status'=>0]);
              }
          }
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
            //print(print_r($idbot,true))."\n";
            $target[] = array(
              'bot_id'=>$idbot,
              'idservice'=>$mid, #id broadcast or reminder
              'message'=>$message,
              'chat_id'=>$row #id from telegram
            );
          }
          
        }
        return $target;
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

/* end console */    
}
