<?php
namespace App\Helpers;
use App\PhoneNumber;

class ApiHelper
{
  public function sendMessage($id_phone_number,$username,$text)
  {
    $phoneNumber = PhoneNumber::find($id_phone_number);
    if (is_null($phoneNumber)){
      return "error";
    }
    
    $curl = curl_init();
    $data = array(
        'token'=> env('TOKEN_API'),
        'phone_number' => $phoneNumber->phone_number,//
        
        'username'=>$username, 
        'message'=>$text, 
        
        'filename'=>$phoneNumber->filename,
    );

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/sendMessage.php",
      CURLOPT_RETURNTRANSFER => 1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => http_build_query($data),
      CURLOPT_POST => 1,
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      // echo $response."\n";
      // print_r($response);
      // return json_decode($response, true);
    }
    
    return "success";
  }

}

?>
