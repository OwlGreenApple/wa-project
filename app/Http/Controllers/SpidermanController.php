<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpidermanController extends Controller
{

  static function url()
  {
    return 'http://103.65.237.93:3001';
  }

  public static function index()
  {
      return view('spiderman.spiderman');
  }

  public static function sendMessage(Request $request)
  {
    $phoneNumber = $request->phone;
    $message = $request->message;
    $url = self::url();
     
    $data = array(
        'to' => $phoneNumber."@c.us",
        'body' => $message
    );
     
    $payload = json_encode($data);
     
    // Prepare new cURL resource
    // $ch = curl_init('http://103.65.237.93:3000/api/whatsapp/chats/sendMessage');
    $ch = curl_init($url.'/api/whatsapp/chats/sendMessage');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'apikey:a802233777d9riz1b11dk7d70531ab99',
        'Content-Length: ' . strlen($payload))
    );

    // Submit the POST request
    $result = curl_exec($ch);
     
    // Close cURL session handle
    curl_close($ch);

    // return "success";
    return $result;
  }

  public static function scan()
  {
    $url = self::url();
    // Prepare new cURL resource
    $ch = curl_init($url.'/api/whatsapp/instance/scan');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'apikey:a802233777d9riz1b11dk7d70531ab99'
    ));

    // Submit the POST request
    $result = curl_exec($ch);

    // Close cURL session handle
    curl_close($ch);

    $qrcode='<img src="data:image/jpeg;base64,'.base64_encode($result).'"/>';
    // return $qrcode;
    return $result;
  }
  
  public static function start()
  {
    // Prepare new cURL resource
    $url = self::url();
    $ch = curl_init($url.'/api/whatsapp/instance/start');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'apikey:a802233777d9riz1b11dk7d70531ab99'
    ));

    // Submit the POST request
    $result = curl_exec($ch);

    // Close cURL session handle
    curl_close($ch);

    // return $qrcode;
    return $result;

  }
  
  public static function status()
  {
    // Prepare new cURL resource
    $url = self::url();
    $ch = curl_init($url.'/api/whatsapp/instance/status');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    // Set HTTP Header for POST request 
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'apikey:a802233777d9riz1b11dk7d70531ab99'
    ));

    // Submit the POST request
    $result = curl_exec($ch);

    // Close cURL session handle
    curl_close($ch);

    // return $qrcode;
    return $result;

  } 
}