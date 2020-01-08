<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\UserList;
use App\BroadCast;
use App\Reminder;
use Carbon\Carbon;

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

        if($botlist->count() > 0)
        {
          foreach($botlist as $list)
          {
            $botapi = $list->bot_api;
            $this->getListBot($botapi,'xxx',1);
            /*
            if($broadcast->count() > 0)
            {
              $this->broadcastMessage($botapi);
            }
            else
            {
              $this->reminderMessage($botapi);
            }
            sleep(2); 
            */
          }
        }
    }

    public function broadcastMessage($botapi)
    {
      $broadcast = BroadCast::where('status','=',0)->get();
      $broadcastidlist = array();

      if($broadcast->count() > 0)
      {
          foreach($broadcast as $rows)
          {
            $broadcastmessage = $rows->message;
            $broadcastid = $rows->id;
            $bcd = $this->getListBot($botapi,$broadcastmessage,$broadcastid);
            $broadcastidlist[] = $bcd;
          }
      }

      /*
      if(count($broadcastidlist) > 0)
      {
          BroadCast::whereIn('id',$broadcastidlist)->update(['status'=>1]);
      }
      */
    }

     public function reminderMessage($botapi)
    {
      $reminder = Reminder::where('status','=',1)->get();
      $reminderidlist = array();

      if($reminder->count() > 0)
      {
          foreach($reminder as $rows)
          {
            $remindermessage = $rows->message;
            $reminderid = $rows->id;

            $rcd = $this->getListBot($botapi,$remindermessage,$reminderid);
            $reminderidlist[] = $rcd;
          }
      }

      if(count($reminderidlist) > 0)
      {
          Reminder::whereIn('id',$reminderidlist)->update(['status'=>1]);
      }
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
          //print(print_r($id,true))."\n";
          foreach(array_unique($id) as $row)
          {
            //print(print_r($idbot,true))."\n";
            $this->sendMessage($row,'testmulti',$idbot);
            sleep(2);
          }
          
        }
        //return $mid;
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
