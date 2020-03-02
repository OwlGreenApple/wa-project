<?php 

namespace App\Classes;

class Telegram
{

  public function getVerify($phone_number,$authcode,$filename) 
  {
    $curl = curl_init();
    $data = array(
        'token'=> '0698a365aec87be50795ab07230d7df55df6eda532b81',
        'phone_number'=>$phone_number,
        'authcode'=>$authcode,
        'filename'=>$filename,
    );

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/auth-verify-phone.php",
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
      return $response."\n";
      // print_r($response);
      // return json_decode($response, true);
    }
  }

  public function test($variables)
  { 
     return $variables.' test';
  }

/* end class */
}

?>