<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
	protected $table = 'lists';
    /*
		status :
		0 = inactive product
		1 = active product
    */
}
