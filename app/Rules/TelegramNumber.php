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
        if(preg_match("/(^|,)([+]|0|[+]0)/i",$value))
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
        return 'Your Number is invalid, please do not use 0 or + as a leading number';
    }
}
