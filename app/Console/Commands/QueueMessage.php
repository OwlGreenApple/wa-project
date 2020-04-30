<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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