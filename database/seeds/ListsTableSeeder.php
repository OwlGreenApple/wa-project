<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('lists')->insert([
        	'user_id' => 1,
            'name' => 'Activflash',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'wa_number' => "+62817318368",
        ]); 

         DB::table('lists')->insert([
        	'user_id' => 1,
            'name' => 'Omnifluencer',
            'is_event'=>1,
            'event_date'=> Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'wa_number' => "+62817318368",
        ]);

         DB::table('lists')->insert([
        	'user_id' => 1,
            'name' => 'Omnilinks',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'wa_number' => "+62817318368",
        ]);
    }

    private function getRandomUserId() {
        $user = User::inRandomOrder()->first();
        return $user->id;
    }
}
