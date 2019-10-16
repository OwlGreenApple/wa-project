<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DeviceController;
use App\Sender;


class CreateDeviceController extends Controller
{
    public function index()
    {
    	return view('device.create-device');
    }

    public function createDevice(Request $request)
    {
    	$curl = curl_init();
    	$data = array(
    		"alias"=>$request->device_name,
    		"billingPlan"=>"gateway-professional",
    		"googleAccount"=>"gunardi.omnifluencer@gmail.com",
    	);

		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://api.wassenger.com/v1/devices",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($data),
		  CURLOPT_HTTPHEADER => array(
		    "content-type: application/json",
		    "token: 11789023944rfefr00sqd"
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

		$user_id - Auth::id();
		$sender = new Sender;
		$sender->user_id = $user_id;
		$sender->name = $user_id;
		$sender->wa_number = $user_id;
		$sender->device_id = $user_id;
		$sender->save();

		if($sender->save() == true)
		{
			return redirect('deviceauthorize/deviceid');
		}
		else
		{
			return redirect('registerdevice')->with('error','Sorry, cannot save device to database please contact admin');
		}
    }

    public function deviceAuthorize()
    {
    	$id = '5d6e15906de1a4001c90a0f4';
    	$authorize = new DeviceController();
    	$authorize->getScanBarcodeAuthorize($id);
    }

    /**/

    public function devicePackage()
    {
    	return view('device.pricing');
    }

    public function checkout()
    {
    	return view('device.checkout');
    }

    public function thankYou()
    {
    	return view('device.thankyou');
    }

    public function temporary()
    {
    	return view('device.temporary');
    }

 /* End controller class */    
}
