<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    /*
    STATUS :
		0 = disabled
		1 = active

		IS_EVENT :
		0 = auto responder
		1 = event
    2 = appointment
    */
}
