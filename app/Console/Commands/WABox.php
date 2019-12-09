<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WABox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:wabox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test to send message to WA through API wabox';

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
        $curl = curl_init();

        $data = array(
            'token'=> 'cefa6dbce7d2ac646733e6954dc2b47a5da4257027ca9',
            'uid'=>6287852700229, //number to send message
            'to'=>62895342472008, // number to receive message
            'text'=>'wabox terminal message 3', //message
            'custom_uid'=>'aa0006', //uuid
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://www.waboxapp.com/api/send/chat",
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

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }
}
