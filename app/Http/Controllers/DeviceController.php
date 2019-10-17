<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Sender;

class DeviceController extends Controller
{

	public function __construct()
    {
        $this->token = '717c449cac6613abd70349cbd889b4955523292e7a45c49ebb2880b9b77e944d44f467389e75a080';
    }

    public function deviceList()
    {
    	$userid = Auth::id();
    	$sender = Sender::where('user_id',$userid)->get();
    	$data = array();

    	if($sender->count() > 0)
    	{
    		foreach ($sender as $row) {
	    		$data['sender'][] =  $row;
	    		$data[$row->id] = $this->getStatusDevice($row->device_id);
	    	}
    	}
    	return view('device.device-list',['data'=>$data]);
    }

    #scan to authorize phone
    public function getScanBarcodeAuthorize($id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wassenger.com/v1/devices/".$id."/scan",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "token: $this->token"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    } 

    #scan to change phone number
    public function getScanBarcodeChangePhone(Request $request)
    {
    	$id =  $request->id;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.wassenger.com/v1/devices/".$id."/scan?force=true",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "token: $this->token"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }

    public function getStatusDevice($deviceid)
    {
    	$curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wassenger.com/v1/devices/".$deviceid."/health",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "token:  ".$this->token.""
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		   return json_decode($err);
		  //echo "cURL Error #:" . $err;
		} else {
		   return json_decode($response,true);
		  //echo $response;
		}
    }

    public function getDetailDevice()
    {
    	$deviceid = '5d6e15906de1a4001c90a0f4';
    	$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wassenger.com/v1/devices/".$deviceid."",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
		    "token: $this->token"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  echo "cURL Error #:" . $err;
		} else {
		  echo $response;
		}
    }

/* end class devicecontroller */    
}
