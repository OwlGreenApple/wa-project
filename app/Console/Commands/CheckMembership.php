<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Mail\MemberShip;

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
        $user = User::all();

        if($user->count() > 0)
        {
            foreach($user as $row)
            {
                if($row->day_left == 5)
                {
                   Mail::to($row->email)->send(new MemberShip($row->day_left));
                }
                elseif($row->day_left == 1)
                {
                   Mail::to($row->email)->send(new MemberShip($row->day_left));
                }
                elseif($row->day_left < 0)
                {
                  Mail::to($row->email)->send(new MemberShip($row->day_left));
                }
            }
        }
    }
}
