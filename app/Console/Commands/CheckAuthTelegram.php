<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use Carbon\Carbon;
use App\User;
use App\PhoneNumber;

class CheckAuthTelegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:authtelegram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To Check Status Connected of phone number';

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
      $phoneNumbers = PhoneNumber::where("status",2)
                        ->get();
      foreach ($phoneNumbers as $phoneNumber) {
        $curl = curl_init();
        $data = array(
            'token'=> env('TOKEN_API'),
            'phone_number' => $phoneNumber->phone_number,
            'username'=>"activtelgroup", 
            'message'=>"123 test", 
            'filename'=>env('FILENAME_API').$phoneNumber->id,
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/check-auth.php",
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => http_build_query($data),
          CURLOPT_POST => 1,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          // echo "cURL Error #:" . $err;
        } else {
          echo $response."\n";
          if ($response == "success"){
            
          }
          if ($response == "fail"){
            $phoneNumber->status = 0;
            $phoneNumber->save();
          }
        }

        sleep(1);
        
      }
      
    }

}
