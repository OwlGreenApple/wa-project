<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CKController extends Controller
{
    public function index()
	{
		return view('ck');
	}
	
	/* CKEDITOR */
	public function ck_browse(){
		$username = Auth::user()->name;
		$id = Auth::id();
		$folder = $username.'-'.$id;

		$path = public_path().'/ckfinder/'.$folder;
		$dir['image'] = scandir($path,1);
		
		$x = 0;
		foreach($dir  as $row=>$val)
		{
			foreach($val as $rows){
				if($val[$x] == '..' || $val[$x] == '.' ){
					unset($val[$x]);
				} 
				$x++;
			}
		}
		return view('ck-browser',['data'=>$val,'folder'=>$folder]);
	}
	
	public function ck_delete_image(Request $request){
		$image_name = $request->filename;
		$path = public_path().'/images/'.$image_name;
	
		if(file_exists($path)){
			unlink($path);
			$data['msg'] = 'Your file : '.$image_name.' has been deleted';
		} else {
			$data['msg'] = 'File is not available';
		}
		return json_encode($data);
	}
	
	public function ck_upload_image(){
		 return;
	}
}
