<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\HelpersApiHelper;
use App\PhoneNumber;
use App\Server;
use App\Helpers\ApiHelper;

class CheckConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check connection whether phone connected or not';

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
        $phone_numbers = PhoneNumber::all();
        if($phone_numbers->count() > 0)
        {
          foreach($phone_numbers AS $row)
          {
            $idphone_number = $row->id;
						$status = false;
						
						if ($row->mode == 0 ) {
							// simi
							$server = Server::where("phone_id",$idphone_number)->first();
							if (!is_null($server)){
								$status_connect = json_decode(ApiHelper::status_simi($server->url));
								if ($status_connect->connected) {
									$status = true;
								}
							}
						}
						if ($row->mode == 1 ) {
							//woowa
							$check_connected = json_decode(ApiHelper::status_nomor($row->phone_number));
							if (!is_null($check_connected)) {
								if ($check_connected['status']=="success") {
									$status = true;
								}
							}
						}
						
						if (!$status) {
							$phone = PhoneNumber::find($idphone_number);
							$phone->status = 1;
							$phone->save();
						}
						
          } // END FOREACH
        }
    }
}
