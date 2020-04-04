<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
  /* OLD
  * Status 
  * 0 -> Not Verified (Not Ready to scan code, mesti tunggu 3-5menit)
  * 1 -> Ready To Scan Code (Belum scan code)
  * 2 -> Verified
  */
  /* NEW
  * Status 
  * 0 -> Disconnected
  * 1 -> Connected
  */
  protected $table = 'phone_numbers';
}
