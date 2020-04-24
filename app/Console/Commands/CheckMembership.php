<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\PhoneNumber;
use App\Mail\MemberShip;
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

        if($users->count() > 0)
        {
            foreach($users as $row)
            {
              $day_left = $row->day_left;
              $client = User::find($row->id);
              if($client->day_left > -2)
              {
                 $client->day_left--;
                 $client->save();
                 $day_left = $client->day_left;
              }

              if($day_left == 0)
              {
                 $client->membership = null;
                 $client->status = 0;
                 $client->save();

                 $phone = PhoneNumber::where('user_id',$row->id);
                 $delete_api = ApiHelper::unreg($phone->first()->phone_number);
                 $phone->delete();
              }

              if($day_left == 5 || $day_left == 1 || $day_left == -1)
              {
                 Mail::to($row->email)->send(new MemberShip($day_left,$row->phone_number,$row->id));
              }
            }
        }
    }
}
