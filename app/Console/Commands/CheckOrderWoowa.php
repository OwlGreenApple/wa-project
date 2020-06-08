<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\HelpersApiHelper;
use App\PhoneNumber;
use App\Order;
use App\InvoiceOrder;
use App\Server;
use App\Helpers\ApiHelper;

use Carbon\Carbon;

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
      $orders = Order::
                where('mode',1)
                ->where('status_woowa',1)
                ->where('month','>',1)
                ->get();
      foreach ($orders as $order){
        echo $order->id." ";
        $dt = Carbon::now();
        $selisih_bulan = $dt->diffInMonths(Carbon::createFromFormat('Y-m-d H:i:s', $order->created_at)) +1;
        echo $selisih_bulan;

        $jumlahInvoiceOrder = InvoiceOrder::where("order_id",$order->id)->count();
        if (($selisih_bulan>$jumlahInvoiceOrder)&&($order->month>$jumlahInvoiceOrder)){
          $order->status_woowa = 0;
          $order->save();
          echo "in";
        }
        echo "\n";
      }
    }
}
