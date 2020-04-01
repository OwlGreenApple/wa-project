<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ImportValidation implements Rule
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
      if(preg_match("/^0\w.*$/i",$value)) //|| !preg_match("/^\+\d/",$value)
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
        return 'Phone numbers must be lead with + NOT 0 and must be number.';
    }
}
