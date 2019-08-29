<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

	protected $table = 'customers';
    /*
		status : 
		0 = inactive / banned
		1 = active
    */
}
