<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
  /*
  * Status 
  * 0 -> Not Verified
  * 1 -> Verify Code Berhasil terkirim
  * 2 -> Verified
  */
  protected $table = 'phone_numbers';
}
