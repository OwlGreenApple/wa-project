<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
	protected $table = 'lists';
  protected $fillable = [
        'label_name'
  ];
    /*
		status :
		0 = inactive product
		1 = active product
    */
}
