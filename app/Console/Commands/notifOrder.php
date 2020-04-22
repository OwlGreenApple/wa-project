<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifyOrder;
use App\User;
use App\Order;
use Carbon\Carbon;
use Date;
use App\Helpers\ApiHelper;

class notifOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notif:order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To notify user to make payment after order';

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
        $users = User::where([['users.status','>',0],['orders2.status','=',0]])->rightJoin('activrespon2.orders AS orders2','orders2.user_id','=','users.id')->select('orders2.*','users.email','users.phone_number')->get();

        if($users->count() > 0)
        {
         foreach($users as $row)
         {
           $date_order = date_create(Carbon::parse($row->created_at)->toDateString());
           $today = date_create(Carbon::now()->toDateString());
           $diff = date_diff($date_order,$today);
           $diffDay = (int)$diff->format('%a');

           if($diffDay == 1 || $diffDay == 5)
           {
              $orders = [
                'no'=>$row->no_order,
                'package'=>$row->package_title,
                'pack'=>$row->package,
                'discount'=>$row->discount,
                'total'=>$row->grand_total,
              ];
              Mail::to($row->email)->send(new NotifyOrder($diffDay,$row->phone_number,$orders));
           }
           sleep(2);
         } // END FOREACH
        }// END IF
    }
}
