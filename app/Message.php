<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
  /* 
  * Status 
  * 0 -> pending
  * 1 -> executed
  */
  protected $table = 'messages';
}
