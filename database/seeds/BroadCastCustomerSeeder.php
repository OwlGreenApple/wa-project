<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BroadCastCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($x=0;$x<=100;$x++)
        {
             DB::table('broad_cast_customers')->insert([
              'broadcast_id' => mt_rand(1,12),
              'customer_id' => mt_rand(1,12),
              'created_at' => Carbon::now(),
              'updated_at' => Carbon::now()
            ]);  
        }
    }
}
