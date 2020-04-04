<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TelNumber implements Rule
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
        if(preg_match("/^0[0-9]*$/i",$value) || !preg_match("/^\+[0-9]/i",$value) || preg_match("/[a-z-A-Z]/i",$value))
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
        return 'Please fill in your Phone number only (ex: 87881115557 ), do not use "0" at the beginning';
    }
}
