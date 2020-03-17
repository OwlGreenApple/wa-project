<?php
namespace App\Helpers;

class Alert
{
    /*SettingController*/

    // (connect_phone)
    public static function one_number(){ return 'Sorry, you can create 1 phone number only';}
    public static function exists_phone(){ return 'Phone Number Already Registered';}
    public static function connect_success(){ return 'Mohon tunggu 3 - 5 menit kemudian silahkan tekan "klik to verify", mohon untuk tidak me-reload atau menutup browser';}

    // (verify_phone)
    public static function registered_phone(){ return 'Error, nomer WA belum terdaftar';}
    public static function qrcode(){ return 'Maaf, untuk sementara tidak dapat menampilkan QR-CODE, silahkan coba kembali, apabila anda baru mendaftar mohon menunggu 3- 5 menit.';}
    public static function phone_connect(){ return 'Your phone had connected';}
    public static function error_verify(){ return 'Sorry there is error on our server, please try again later';}
}

?>