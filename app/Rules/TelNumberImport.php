<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class TelNumberImport implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->phone_number = $phone_number;
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
        // return $this->checkNumber($value);
        dd($this->checkNumber($value));
    }

    public function checkNumber($value)
    {
        
        if(preg_match("/^0[0-9]*$/i",$value) || !preg_match("/^[+][0-9]/i",$value) || preg_match("/[a-z]/i",$value))
        {
           return false;
        }
        else
        {
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
        return 'Phone numbers must be lead with + NOT 0 and must be number';
    }
}
