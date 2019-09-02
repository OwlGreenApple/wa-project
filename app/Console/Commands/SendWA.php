<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use App\Customer;
use Carbon\Carbon;
use App\User;

class SendWA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:wa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send whatsapp message to customer according on broadcast or reminder customer';

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
        
        /* Users counter */
        $user = User::select('id','counter')->get();
        foreach($user as $userow){
            $id_user = $userow->id;
            $count = $userow->counter;
            $broadcast_customers = BroadCastCustomers::where([
                ['user_id','=',$id_user],
                ['status','=',0],
            ])->orderBy('id','asc');

            /* Broadcast */
            if($broadcast_customers->count() > 0){
                /* get user id where status = 0 asc */
                $id_broadcast = $broadcast_customers->take($count)->get();
                foreach($id_broadcast as $id){
                      //some code to call wassenger here....
                      $update_broadcast = BroadCastCustomers::where('id',$id->id)->update(['status'=>1]);
                      if($update_broadcast == true){
                            $count = $count - 1;
                            User::where('id',$id_user)->update(['counter'=>$count]);
                      } else {
                            echo 'Error!! Unable to update broadcast customer';
                      }
                }
            } else {
            /* Reminder */
               
               $reminder_customers = ReminderCustomers::where([
                    ['user_id','=',$id_user],
                    ['status','=',0],
                ])->orderBy('id','asc');

               /* get days from reminder */
                $reminder = Reminder::where('reminders.user_id','=',$id_user)
                                ->rightJoin('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
                                ->rightJoin('customers','customers.id','=','reminder_customers.customer_id')
                                ->select('reminder_customers.*','reminders.days','reminders.created_at as datecr','customers.created_at AS cstreg')
                                ->get();

                /* check date reminder customer and update if succesful sending */
                foreach($reminder as $col) {
                    $date_reminder = Carbon::parse($col->datecr); //date when reminder was created
                    $day_reminder = $col->days; // how many days
                    $customer_signup = Carbon::parse($col->cstreg);
                    $adding = $customer_signup->addDays($day_reminder);

                    if($date_reminder <= $adding){
                        //some code to call wassenger here....
                        ReminderCustomers::where('id',$col->id)->update(['status'=>1]);
                    } 
                }
            }

        /* end user looping */
        }

    /* End function handle */    
    }
/* End command class */    
}
