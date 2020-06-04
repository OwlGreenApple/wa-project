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
			//status 6 dari campaign controller
			//Simi
			$messages = Message::where("status",6)->get();
			foreach($messages as $message) {
				$send_message = ApiHelper::send_simi($message->phone_number,$message->message,$message->key);
				$status = $this->getStatus($send_message,0);
				
				$message->status = $status;
				$message->save();

				sleep(10);
			}
			
			//status 7 dari campaign controller
			//woowa
			$messages = Message::where("status",7)->get();
			foreach($messages as $message) {
				$send_message = ApiHelper::send_message($message->phone_number,$message->message,$message->key);
				$status = $this->getStatus($send_message,1);
				
				$message->status = $status;
				$message->save();

				sleep(10);
			}
			
			//status 8 dari customer controller, perlu untuk mengubah status customer
			//simi
			$messages = Message::where("status",8)->get();
			foreach($messages as $message) {
				$send_message = ApiHelper::send_simi($message->phone_number,$message->message,$message->key);
				$status = $this->getStatus($send_message,0);
				
				$message->status = $status;
				$message->save();

				$newcustomer = Customer::find($message->customer_id);
				$newcustomer->status = 0;
				$newcustomer->save();

				sleep(10);
			}
			
			//status 9 dari customer controller, perlu untuk mengubah status customer
			//woowa
			$messages = Message::where("status",9)->get();
			foreach($messages as $message) {
				$send_message = ApiHelper::send_message($message->phone_number,$message->message,$message->key);
				$status = $this->getStatus($send_message,1);
				
				$message->status = $status;
				$message->save();

				$newcustomer = Customer::find($message->customer_id);
				$newcustomer->status = 0;
				$newcustomer->save();

				sleep(10);
			}

		}
 
    public function getStatus($send_message,$mode)
    {
			//default status 
			$status = 2;
			
			if ($mode == 0) {
				//status simi
				$obj = json_decode($send_message);
				// if (method_exists($obj,"sent")) {
				if (is_callable($obj, true, "sent")) {
					if ($obj->sent) {
						$status = 1;
					}
					else {
						//number not registered
						$status = 3;
					}
				}
				// if (method_exists($obj,"detail")) {
				if (is_callable($obj, true, "detail")) {
						//dari simi whatsapp instance is not running -> phone_offline
						$status = 2;
				}
			}
			
			if ($mode == 1) {
				//status woowa
				if(strtolower($send_message) == 'success')
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
			}

      return $status;
    }
/* End command class */    
}