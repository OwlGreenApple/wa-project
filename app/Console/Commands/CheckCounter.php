<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PhoneNumber;
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
    protected $description = 'To reset counter every minute';

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
        $phoneNumber = PhoneNumber::select('counter','user_id')->get();

        if($phoneNumber->count() > 0){
            foreach($phoneNumber as $row){
                $counter = $row->counter;
                if($counter < env('MAXIMUM_COUNTER')){
                    $update = PhoneNumber::where('user_id',$row->user_id)->update(['counter'=>env('MAXIMUM_COUNTER')]);
                }
            }
        }
    }

/* End check counter */
}
