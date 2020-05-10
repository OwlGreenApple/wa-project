<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
						$broadcastcustomer = new BroadCastCustomers;
						$broadcastcustomer->broadcast_id = $this->broadcast_id;
						$broadcastcustomer->customer_id = $col->id;
						$broadcastcustomer->save();
				}
			}
		}
}
