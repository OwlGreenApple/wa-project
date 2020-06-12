<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\UserList;
class UserList extends Model
{
	protected $table = 'lists';
    /*
		status :
		0 = inactive product
		1 = active product
    */

  public static function create_link_unsubs($id){
    $list = UserList::find($id);
    if (!is_null($list)){
      if ($list->link_unsubs =="")
      {
        $data = array(
          "to" => "https://activrespon.com",
        );

        $payload = json_encode($data);

        // Prepare new cURL resource
        $ch = curl_init('https://spon.li/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        // Set HTTP Header for POST request 
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );

        // Submit the POST request
        $result = curl_exec($ch);

        // Close cURL session handle
        curl_close($ch);
        $obj = json_decode($result);

        $list->link_unsubs = $obj->url;
        $list->save();
      }
      
      return $list->link_unsubs;
    }
    
    return false;
  }
}



/*
Google Script 
function init() {
	list_name ="";
	myFunctionpost(list_name);
}

function lastValue(column) {
  var lastRow = SpreadsheetApp.getActiveSheet().getMaxRows();
  var values = SpreadsheetApp.getActiveSheet().getRange(column + "1:" + column + lastRow).getValues();

  for (; values[lastRow - 1] == "" && lastRow > 0; lastRow--) {}
  return values[lastRow - 1];
}

function myFunctionpost(list_name) {
  var url = "https://activrespon.com/dashboard/entry-google-form";
  var b=lastValue("b");
  var c=lastValue("c");
  var d=lastValue("d");
  Logger.log(b.toString());  
  var data = {"list_name":list_name,"name": b.toString(), 
              "email": d.toString(), "phone_number": c.toString()};
  var payload = JSON.stringify(data);

  var headers = { "Accept":"application/json", 
              "Content-Type":"application/json", 
              "Authorization":"Basic _authcode_"
             };


  var options = { "method":"POST",
             "contentType" : "application/json",
            "headers": headers,
            "payload" : payload
           };
  var response = UrlFetchApp.fetch(url, options);
  Logger.log(response);
}


obfuscate one 
https://obfuscator.io/
function myFunctionpost(list_name) {
var _0x2799=['https://activrespon.com/dashboard/entry-google-form','fetch','application/json','toString','log','stringify','Basic\x20_authcode_'];(function(_0x36ee5d,_0x279935){var _0x646ae4=function(_0x1dc43e){while(--_0x1dc43e){_0x36ee5d['push'](_0x36ee5d['shift']());}};_0x646ae4(++_0x279935);}(_0x2799,0x14a));var _0x646a=function(_0x36ee5d,_0x279935){_0x36ee5d=_0x36ee5d-0x0;var _0x646ae4=_0x2799[_0x36ee5d];return _0x646ae4;};var url=_0x646a('0x6');var b=lastValue('b');var c=lastValue('c');var d=lastValue('d');Logger[_0x646a('0x3')](b['toString']());var data={'list_name':list_name,'name':b[_0x646a('0x2')](),'email':d[_0x646a('0x2')](),'phone_number':c[_0x646a('0x2')]()};var payload=JSON[_0x646a('0x4')](data);var headers={'Accept':'application/json','Content-Type':'application/json','Authorization':_0x646a('0x5')};var options={'method':'POST','contentType':_0x646a('0x1'),'headers':headers,'payload':payload};var response=UrlFetchApp[_0x646a('0x0')](url,options);Logger[_0x646a('0x3')](response);
}

*/