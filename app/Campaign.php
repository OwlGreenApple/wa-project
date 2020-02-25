<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
  protected $table = 'campaigns';
    /*
    Type 
    0 -> Event 
    1 -> Auto Responder 
    2 -> Event 
    */
}
