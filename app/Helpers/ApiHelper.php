<?php
namespace App\Helpers;
use App\PhoneNumber;

class ApiHelper
{

  static function bar(){
    return 'fb6d0ba27c5170239c7bc08f043e985eee2c913b997ada89';
  }
  
  public function go_curl($url,$data,$method)
  {
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

  public static function reg($no_wa,$nama)
  {
    $url='https://116.203.92.59/api/async_whatsapp_api_reg';
    // $url='https://116.203.92.59/api/whatsapp_api_reg';
    $key= self::bar(); // key partner

    $data = array(
      "no_whatsapp" => $no_wa,
      "key"=>$key,
      "nama"=>$nama,
      "no_telegram"=>$no_wa //no telegram partner untuk info jika ada server down dll
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
    //echo $res."\n";
    return json_encode(['message'=>$res]);
  }

  public static function get_qr_code($no_wa)
  {
    $url='https://116.203.92.59/api/generate_qr';
    $key= self::bar();
    $url_img='https://116.203.92.59/images/'.$key.'/';
  
    $data = array(
      "no_wa" => $no_wa,
      "key"=>$key,
    );

    $_this = new self;
    $filename= $_this->go_curl($url,$data,"POST");

    $res_arr=array('not_valid_ip','failed','port_down');
    if (!in_array($filename,$res_arr)) {
      $file_code=$_this->go_curl($url_img.$filename,$data,"GET");
        $qrcode='<img src="data:image/jpeg;base64,'.base64_encode($file_code).'"/>';
        return $qrcode;
    }else{
        $qrcode=$filename;
        return false;
    }

    //echo $qrcode;
  }

  public static function status_nomor($no_wa)
  {
      $url='https://116.203.92.59/api/status_nomor';
      $key=self::bar();

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

      return curl_exec($ch);
      // echo $res=curl_exec($ch);
  }

  public static function qr_status($no_wa)
  {
    $url='https://116.203.92.59/api/qr_status';
    $key= self::bar();

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

    // echo $res=curl_exec($ch);
    return curl_exec($ch);
  }
  
  public static function get_all_cust()
  {
    $url='https://116.203.92.59/api/get_all_cust_expired';
    // $url='https://116.203.92.59/api/get_all_cust';
    $key= self::bar();
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
    $key= self::bar();

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
  
  public static function unreg($no_wa)
  {
    $url='https://116.203.92.59/api/unreg';
    $key= self::bar();

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

    return curl_exec($ch);
    // echo $res=curl_exec($ch);
  }

  public function driver_restart($no_wa)
  {
    $url='https://116.203.92.59/api/driver_restart';
    $key= self::bar();

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

  public function get_status_by_message_id($no_wa,$wa_id)
  {
    $url='https://116.203.92.59/api/bulk_check_id';
    $key= self::bar();

    $data = array(
      "no_wa" => $no_wa,
      "key"=>$key,
      "bulk_msg_id"=>$wa_id
      // "bulk_msg_id"=>'15590662993884,15590662985373'
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

  public static function get_key($no_wa)
  {
    $url='https://116.203.92.59/api/get_ip_key';

    $key= self::bar(); // key partner

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

    $res=curl_exec($ch);
    //echo $res."\n";
    return json_encode(['message'=>$res]);
  }

  public static function take_screenshot($no_wa)
  {
    $url='https://116.203.92.59/api/take_screenshot';
    $key= self::bar(); // key partner
    $url_img='https://116.203.92.59/images/'.$key.'/';
    $data = array(
      "no_wa" => $no_wa,
      "key"=>$key,
    );

    $_this = new self;
    $filename= $_this->go_curl($url,$data,"POST");


    $res_arr=array('not_valid_ip','failed','port_down');
    if (!in_array($filename,$res_arr)) {
      $file_code=$_this->go_curl($url_img.$filename,$data,"GET");
      $qrcode='<img src="data:image/jpeg;base64,'.base64_encode($file_code).'"/>';
    }else{
        $qrcode=$filename;
    }

    return $qrcode;
  }

  public static function send_message($phoneNumber,$message,$key)
  {
    $url='http://116.203.92.59/api/send_message';
    $data = array(
      "phone_no"=> $phoneNumber,
      "key"		=>$key,
      "message"	=>$message
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
    curl_close($ch);
    
    return $res;
  }

  public static function send_message_android($cs_id,$message,$phone_number,$type)
  {
    $data = array(
      'app_id' => '429d3472-da0f-4b2b-a63e-4644050caf8f', //app id don't change
      'include_player_ids' => [$cs_id], //you can take Player id from Woowandroid App CS ID menu.
      'data' => array(
          "type"      => $type, //opsional Reminder/After Checkout/Pending Payment/dll editable
          "message"   => $message,
          "no_wa"     => $phone_number
      ),
      'contents'  => array(
          "en"    => 'Woowa Title'
      ),
      "headings"  =>  array(
          "en"    => 'Woowa Notice'
      )
    );
    $data_json = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json; charset=utf-8',
        'Authorization: Basic NjY0NzE3MTYtMzc3ZC00YmY5LWJhNzQtOGRiMWM1ZTNhNzBh')); //os_auth don't change
    $response = curl_exec($ch);
    curl_close($ch);
    // echo $response;
  }

  public static function send_image_url($phoneNumber,$url_image,$message,$key)
  {
    $url='http://116.203.92.59/api/send_image_url';
    $data = array(
      "phone_no"=> $phoneNumber,
      "key"		=>$key,
      "url"	=>$url_image,
      "message"	=>$message
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
    curl_close($ch);
    
    return $res;
  }

/* END CLASS */
}