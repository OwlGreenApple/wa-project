<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use App\Customer;
use App\UserList;
use Session;

 /* 
    To check wa number according on :
    - list / product
    - wa number that inserted
 */

class CheckWANumbers implements Rule
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
        return $this->checkwanumbers($value);
    }

    public function checkwanumbers($value){
        //retrieve session from userlist
        $userlist =  Session::get('userlist');
        Session::reflash();
        $get_id_list = UserList::where('name','=',$userlist)->first();
        $id_user_list = $get_id_list->id;

        $checkwa = Customer::where([
                    ['wa_number','=',$value],
                    ['list_id','=',$id_user_list]
                    ])->first();

        if(is_null($checkwa)){
            return true;
        } else {
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
        return 'Sorry, this number has already been taken..';
    }
}
