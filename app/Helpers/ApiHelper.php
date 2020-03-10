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
  
  public function go_curl($url,$data,$method){
    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
    );
    $res=curl_exec($ch);
    return $res;
  }

  public function get_qr_code()
  {
    $url='https://116.203.92.59/api/generate_qr';
    $key='fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89';
    $url_img='https://116.203.92.59/images/'.$key.'/';
    $no_wa='+6285704701230';
    $data = array(
      "no_wa" => $no_wa,
      "key"=>$key,
    );

    $filename=$this->go_curl($url,$data,"POST");


    $res_arr=array('not_valid_ip','failed','port_down');
    if (!in_array($filename,$res_arr)) {
      $file_code=$this->go_curl($url_img.$filename,$data,"GET");
        $qrcode='<img src="data:image/jpeg;base64,'.base64_encode($file_code).'"/>';
    }else{
        $qrcode=$filename;
    }
    echo $qrcode;
  }
  
  public function get_client()
  {
    $url='https://116.203.92.59/api/get_all_cust';
    $key='fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89';
    $data = array(
      "key"=>$key
    );

    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
    );
    $res=curl_exec($ch);
    echo $res."\n";
  }

  public function billing()
  {
    $url='https://api.woo-wa.com/v2.0/get-billing';
    $key='fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89';

    $data = array(
      "key"=>$key
    );

    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $res=curl_exec($ch);
    echo $res."\n";
  }
  
  public function reg()
  {
    $url='https://116.203.92.59/api/whatsapp_api_reg';
    $key='fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89'; // key partner

    $no_wa='+6285704701230'; // no wa pelanggan
    $nama='tes mike1'; // nama pelanggan
    $data = array(
      "no_whatsapp" => $no_wa,
      "key"=>$key,
      "nama"=>$nama,
      "no_telegram"=>"082230779167" //no telegram partner untuk info jika ada server down dll
    );

    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
    );
    $res=curl_exec($ch);
    echo $res."\n";
  }
  
  public function unreg()
  {
    $url='https://116.203.92.59/api/unreg';

    $key='fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89';
    $no_wa='+6285704701230';

    $data = array(
      "no_wa" => $no_wa,
      "key"=>$key,
    );

    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 360);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
    );

    echo $res=curl_exec($ch);
  }
}

?>
