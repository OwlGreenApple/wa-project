<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\PhoneNumber;

class AvailablePhoneNumber implements Rule
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
        return $this->checkPhoneNumber($value);
    }

    public function checkPhoneNumber($value)
    {
        $userid = Auth()->id();
        $phone = PhoneNumber::where([['user_id',$userid],['phone_number',$value]])->first();

        if(is_null($phone))
        {
          return true;
        }
        else {
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
        return 'You cannot update using connected number';
    }
}
