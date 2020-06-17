<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
  /* 
  *counter -> buat counter maximum dalam 1 menit
  *counter2 -> buat counter akan stop setelah 2-3 menit setelah counter abis
  
  
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
