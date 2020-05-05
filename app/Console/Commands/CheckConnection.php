<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\HelpersApiHelper;
use App\PhoneNumber;

class CheckConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check connection whether phone connected or not';

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
        $phone_numbers = PhoneNumber::all();
        if($phone_numbers->count() > 0)
        {
          foreach($phone_numbers AS $rows)
          {
            $idphone_number = $rows->id;
            $check_connected = ApiHelper::status_nomor($rows->phone_number);
            if($check_connected <> 'success')
            {
              $phone = PhoneNumber::find($idphone_number);
              $phone->status = 1;
              $phone->save();
            }
          } // END FOREACH
        }
    }
}
