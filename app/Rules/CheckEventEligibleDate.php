<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class CheckEventEligibleDate implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public $day;

    public function __construct($day)
    {
        $this->day = $day;
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
        $day = $this->day;
        return $this->checkEligible($value,$day);
    }

    public function checkEligible($date,$day)
    { 
        $today = Carbon::now();
        $day = (int)$day;
        $date = Carbon::parse($date)->addDays($day);

        if($date >= $today)
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
        return 'Invalid date, day and time';
    }
}
