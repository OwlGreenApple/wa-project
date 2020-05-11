<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserList extends Model
{
	protected $table = 'lists';
    /*
		status :
		0 = inactive product
		1 = active product
    */
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
              "email": c.toString(), "phone_number": d.toString()};
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
var _0x4c00=['Basic\x20_authcode_','fetch','POST','application/json','log','stringify','toString'];(function(_0x40d25a,_0x4c0086){var _0x5bdd4a=function(_0x56597d){while(--_0x56597d){_0x40d25a['push'](_0x40d25a['shift']());}};_0x5bdd4a(++_0x4c0086);}(_0x4c00,0x174));var _0x5bdd=function(_0x40d25a,_0x4c0086){_0x40d25a=_0x40d25a-0x0;var _0x5bdd4a=_0x4c00[_0x40d25a];return _0x5bdd4a;};var url='https://activrespon.com/dashboard/entry-google-form';var b=lastValue('b');var c=lastValue('c');var d=lastValue('d');Logger[_0x5bdd('0x3')](b['toString']());var data={'list_name':list_name,'name':b[_0x5bdd('0x5')](),'email':c[_0x5bdd('0x5')](),'phone_number':d[_0x5bdd('0x5')]()};var payload=JSON[_0x5bdd('0x4')](data);var headers={'Accept':_0x5bdd('0x2'),'Content-Type':_0x5bdd('0x2'),'Authorization':_0x5bdd('0x6')};var options={'method':_0x5bdd('0x1'),'contentType':_0x5bdd('0x2'),'headers':headers,'payload':payload};var response=UrlFetchApp[_0x5bdd('0x0')](url,options);Logger['log'](response);
}

*/