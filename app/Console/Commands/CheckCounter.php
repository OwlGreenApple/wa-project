<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class CheckCounter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:counter';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check counter on table user if sufficient or not to run WA, currently maximum counter is : 6, so if counter below 6 it will refresh or update column into 6';

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
        $user = User::select('counter','id')->get();
        foreach($user as $row){
            $counter = $row->counter;
            if($counter < 6){
                $update = User::where('id',$row->id)->update(['counter'=>6]);
            }
        }
    }

/* End check counter */
}
