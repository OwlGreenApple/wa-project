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
    public static function registered_phone(){ return 'Error, nomer WA belum terdaftar';}
    public static function qrcode(){ return 'Maaf, untuk sementara tidak dapat menampilkan QR-CODE, silahkan coba kembali, apabila anda baru mendaftar mohon menunggu 3- 5 menit.';}
    public static function phone_connect(){ return 'Your phone had connected';}
    public static function error_verify(){ return 'Sorry there is error on our server, please try again later';}

    /*EventController*/
   /* public static function no_message(){ return 'Campaign ini tidak memiliki message';}
    public static function no_sending_message(){ return 'Campaign ini tidak memiliki message yang terkirim';}*/
}

?>
