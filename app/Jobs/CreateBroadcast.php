<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// use Illuminate\Http\Request;
use App\BroadCastCustomers;
class CreateBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

		protected $customers,$broadcast_id;
		
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customers,$broadcast_id)
    {
        $this->customers = unserialize($customers);
        $this->broadcast_id = $broadcast_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

			if ($this->attempts() == 1) {
				foreach($this->customers as $col){
						// $timegenerate = Carbon::now();
						// $logexists = Storage::disk('local')->exists('job/log.txt');

						// if($logexists == true)
						// {
								// $log = Storage::get('job/log.txt');
								// $string = $log."\n".", Date and time : ".$timegenerate.print_r($this->customers, true);
								// Storage::put('job/log.txt',$string);
						// }
						// else
						// {
								// $string = ", Date and time : ".$timegenerate.print_r($this->customers, true);
								// Storage::put('job/log.txt',$string);
						// }					
					
						$broadcastcustomer = new BroadCastCustomers;
						$broadcastcustomer->broadcast_id = $this->broadcast_id;
						$broadcastcustomer->customer_id = $col->id;
						$broadcastcustomer->save();
				}
			}
		}
}
