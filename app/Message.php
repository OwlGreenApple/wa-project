<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Message;
class Message extends Model
{
  /* 
  * Status 
  * 0 -> pending
  * 1 -> executed
  */
  protected $table = 'messages';
	public static function create_message($phone_number,$message,$key){
    $message_send = new Message;
    $message_send->phone_number=$phone_number;
    $message_send->message= $message;
    $message_send->key=$key;
    $message_send->status=10;
    $message_send->customer_id=0;
    $message_send->save();
    return $message_send;
  }
}
