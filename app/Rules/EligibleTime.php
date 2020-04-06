<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class EligibleTime implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $day_send;
    public function __construct($day_send)
    {
        $this->day = $day_send;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $day = Carbon::parse($this->day)->toDateString();
        $today = Carbon::now();
        $time = explode(':',$value);
        $destination_day = explode('-',$day);

        $hour = (int)$time[0];
        $min = (int)$time[1];
        $year = (int)$destination_day[0];
        $month = (int)$destination_day[1];
        $date = (int)$destination_day[2];

        $destination_time = Carbon::create($year,$month,$date,$hour,$min,0);

        if($destination_time->gte($today))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Time cannot be less than today\'s current time.';
    }
}
