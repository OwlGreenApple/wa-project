<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
  /* 
  * Status 
  * 1 -> Disconnected
	* 2 -> Connected
	* 3 -> woowa not your client

	
	mode 
	0 -> Simi
	1 -> Woowa
	
	if mode == 0 -> filename -> ""
	if mode == 1 -> filename -> key
  */
	
  protected $table = 'phone_numbers';
}
