<?php
namespace App\Helpers;
 
class Alert
{
    /*SettingController*/

    // (connect_phone)
    public static function one_number(){ return 'Sorry, you can create 1 phone number only';}
    public static function exists_phone(){ return 'Phone Number Already Registered';}
    public static function connect_success(){ return '<div class="">Your connection process has started :<br>1. Please wait up to 6 minutes for QR code to appear<br>  ( Do not Refresh or Close your browser )<br>2. Login to your Whatsapp & Scan the QR Code <b><h5><span id="min"></span> : <span id="secs"></span></h5></b></div>';}

    // (verify_phone)
    public static function registered_phone(){ return 'Error, your phone number not registered yet';}
    public static function qrcode(){ return 'Sorry, currently our server is busy, please wait until 3- 5 minutes.';}
    public static function phone_connect(){ return 'Your phone had connected';}
    public static function error_verify(){ return 'Sorry there is error on our server, please try again later';}

    public static function message_status($status)
    {
        if($status == 1)
        {
          return 'Success';
        }
        elseif($status == 2)
        {
          return '<span class="act-tel-apt-create">Phone Offline</span>';
        }
        elseif($status == 3)
        {
          return '<span class="act-tel-apt-create">Phone Not Available</span>';
        }
        else
        {
          return '<span class="act-tel-apt-create">Cancelled</span>';
        }
    }

}

?>
