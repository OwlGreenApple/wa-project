<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TelegramNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return $this->checkNumber($value);
    }

    public function checkNumber($value)
    {
        //PREVENT VALIDATION IF PHONE NUMBER EMPTY ON IMPORT CSV CASE
        if($value == null)
        {
            return true;
        }

        if(preg_match("/(^|,)(0|[+]0)/i",$value) || !preg_match("/^[+][0-9]*$/i",$value))
        {
           return false;
        } 
        else {
           return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your Number is invalid, please use + as a leading number following by your country code and don\'t use 0 as leading number ';
    }
}
