<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

	protected $table = 'customers';
	protected $fillable = ['user_id','list_id','name','email','telegram_number','additional','status'];
    /*
		status : 
		0 = inactive / banned
		1 = active
    */
}
