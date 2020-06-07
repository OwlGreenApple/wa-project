<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\HelpersApiHelper;
use App\PhoneNumber;
use App\Order;
use App\Server;
use App\Helpers\ApiHelper;

class CheckOrderWoowa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:orderwoowa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check order woowa on db';

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
      // $orders = Order::
                // where('mode',1)
                // ->where('status_woowa',1)
                // ->where('month','>',1)
                // ->get();
      // foreach ($orders as $order){
      // }
    }
}
