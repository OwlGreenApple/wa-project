<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Customer;
use App\Message;
use App\Helpers\Spintax;
use Carbon\Carbon;

use DB;
use App\Helpers\ApiHelper;

class QueueMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message message to customer on queue';

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
			//status 9 dari customer controller, perlu untuk mengubah status customer
			$messages = Message::where("status",9)->get();
			foreach($messages as $message) {
				$send_message = ApiHelper::send_message($message->phone_number,$message->message,$message->key);
				$status = $this->getStatus($send_message);
				
				$message->status = $status;
				$message->save();

				$newcustomer = Customer::find($message->customer_id);
				$newcustomer->status = 0;
				$newcustomer->save();

				sleep(10);
			}
			
			//status 0 dari campaign controller
			$messages = Message::where("status",0)->get();
			foreach($messages as $message) {
				$send_message = ApiHelper::send_message($message->phone_number,$message->message,$message->key);
				$status = $this->getStatus($send_message);
				
				$message->status = $status;
				$message->save();

				sleep(10);
			}
		}
 
    public function getStatus($send_message)
    {
      if(strtolower($send_message) == 'Success')
      {
          $status = 1;
      }
      elseif($send_message == 'phone_offline')
      {
          $status = 2;
      } 
      else
      {
          $status = 3;
      }

      return $status;
    }
/* End command class */    
}