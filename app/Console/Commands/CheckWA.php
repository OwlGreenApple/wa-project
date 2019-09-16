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

class CheckWA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:wa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To obtain delivery status from WA and then used it data to update on query status';

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
        $user = Sender::select('id','user_id')->get();
        $getWA = null;
        $delivery_status = null;

        foreach($user as $userow){
            $id_user = $userow->id;

            $broadcastcustomer = BroadCastCustomers::where([
                    ['user_id',$id_user],
                    ['status','=',1],
            ])->select('id_wa')->get();

            if($broadcastcustomer->count() > 0){
                /* Broadcast */
                foreach($broadcastcustomer as $row){
                    $id_wa =  $row->id_wa;
                    $getWA = $this->getWA($id_wa);

                    if($getWA !== null)
                    {
                        $delivery_status = $getWA->deliveryStatus;
                        //check delivery status
                        if($delivery_status == 'queued'){
                            $status = 1;
                        } elseif($delivery_status == 'sent') {
                            $status = 2;
                        } elseif($delivery_status == 'failed') {
                            $status = 5;
                        } else {
                            $status = 0;
                        }
                        $updatebroadcastcustomer = BroadCastCustomers::where([
                            ['id_wa',$id_wa],
                        ])->update(['status'=>$status]);
                    }
                }
            } else {
                /* Reminder */
                $getWAreminder = null;
                $remindercustomer = ReminderCustomers::where([
                    ['user_id',$id_user],
                    ['status',1],
                ])->select('id_wa')->get();

                foreach($remindercustomer as $cols){
                     $id_wa =  $cols->id_wa;
                     $getWAreminder = $this->getWA($id_wa);

                     if($getWAreminder !== null)
                     {
                         $delivery_status = $getWAreminder->deliveryStatus;
                         //check delivery status
                         if($delivery_status == 'queued'){
                            $status = 1;
                         } elseif($delivery_status == 'sent') {
                            $status = 2;
                         } elseif($delivery_status == 'failed') {
                            $status = 5;
                         } else {
                            $status = 0;
                         }

                        $updateremindercustomer = ReminderCustomers::where([
                            ['id_wa',$id_wa]
                        ])->update(['status'=>$status]);
                     }
                }
            }

        /* end user loop */    
        }
    }

     /* get wa status */
     public function getWA($id_wa){
       $curl = curl_init();
        $api_key = '717c449cac6613abd70349cbd889b4955523292e7a45c49ebb2880b9b77e944d44f467389e75a080';

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wassenger.com/v1/messages/".$id_wa."",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "token: ".$api_key.""
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          return json_decode($response);
        }
    }

}
