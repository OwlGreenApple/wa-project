<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

	protected $table = 'customers';
	protected $fillable = ['user_id','list_id','name','email','telegram_number','code_country','additional','status'];
    /*
		status : 
		0 = inactive / banned
		1 = active
    telegram_number = unique
    */
}
