<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\PhoneNumber;
use App\Membership;
use App\Mail\MemberShip as EmailMember;
use App\Helpers\ApiHelper;

class CheckMembership extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:membership';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Membership Valid Until';

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
        $users = User::where('day_left','>',-2)->get();
        $membership = $remain_day_left = 0;

        if($users->count() > 0)
        {
            foreach($users as $row):

              $day_left = $row->day_left;
              $user_id = $row->id;

              $membership = Membership::where([['user_id',$user_id],['status','>',0]])->get();
              $membership = $membership->count();
              $client = User::find($user_id);

              if($day_left == 0 && $membership > 0)
              {
                 continue;
              }

              if($client->day_left > -2)
              {
                 $client->day_left--;
                 $client->save();
                 $remain_day_left = $client->day_left;
              }

              if($membership == 0 && $remain_day_left <= 0)
              {
                 $client->membership = null;
                 $client->status = 0;
                 $client->save();

                 $phone = PhoneNumber::where('user_id',$row->id)->first();
                 if(!is_null($phone))
                 {
                    $phone->counter = 0;
                    $phone->max_counter = 0;
                    $phone->max_counter_day = 0;
                    $phone->status = 0;
                    $phone->save();
                 }
              }

              if(($day_left == 5 || $day_left == 1 || $day_left == -1) && $membership == 0)
              {
                 Mail::to($row->email)->send(new EmailMember($day_left,$row->phone_number,$row->id));
              }
            endforeach;
        }
    }
}
