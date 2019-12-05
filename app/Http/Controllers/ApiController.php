<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserList;
use App\Customer;

class ApiController extends Controller
{
    public function testapi()
    {
    	$curl = curl_init();

        $data = array(
            'list_id'=> '3',
            'wa_no'=>6287852700229,
            'name'=>'test',
            'email'=>'test@mail.com'
        );

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://localhost/waku/private-list",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($data),
          CURLOPT_HTTPHEADER => array('Content-Type:application/json'),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response."\n";
        }
    }

    public function register_list(Request $request)
    {
    	 $data = json_decode($request->getContent(),true);
    	 
    	 $user_id = UserList::where('id',$data['list_id'])->first();
    	 if(is_null($user_id))
    	 {
    	 	$msg['is_error'] = 'Id not available, it may Deleted!!!';
    	 	return json_encode($msg);
    	 }
    	 $userid = $user_id->user_id;
    	 $cust = new Customer;
    	 $cust->user_id = $userid;
    	 $cust->list_id = $data['list_id'];
    	 $cust->name = $data['name'];
    	 $cust->email = $data['email'];
    	 $cust->wa_number = $data['wa_no'];
    	 $cust->save();

    	 if($cust->save())
    	 {
    	 	$msg['is_error'] = 0;
    	 }
    	 else
    	 {
    	 	$msg['is_error'] = 1;
    	 }
    	 return json_encode($msg);
    }

/* end class */    
}
