<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
  /*
  * Status 
  * 0 -> Not Verified (Not Ready to scan code, mesti tunggu 3-5menit)
  * 1 -> Ready To Scan Code (Belum scan code)
  * 2 -> Verified
  */
  protected $table = 'phone_numbers';
}
