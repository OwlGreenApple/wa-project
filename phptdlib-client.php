<?php 

class testTelegram(){

  public function getVerify() {
    $curl = curl_init();
    $data = array(
        'token'=> '0698a365aec87be50795ab07230d7df55df6eda532b81',
        'phone_number'=>'+62895342472008',
        'authcode'=>'87151',
        // 'phone_number'=>'+6287723238793',
        // 'phone_number'=>'+6287855915535',
        
        // 'filename'=>'tlr1',
        // 'filename'=>'tlg11',
        'filename'=>'tllgun1',
        // tdlib tdlib2 tdlib3 tdlib5
    );

    curl_setopt_array($curl, array(
     // CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/auth-set-phone.php",
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

/* end class */
}

?>