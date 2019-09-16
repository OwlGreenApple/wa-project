<?php

namespace App\Http\Controllers;

//require $_SERVER['DOCUMENT_ROOT'].'/waku/assets/ckfinder/core/connector/php/vendor/autoload.php';
//require $_SERVER['DOCUMENT_ROOT'].'/waku/app/ckfinder/core/connector/php/vendor/CKSource/CKFinder/CKFinder.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\UserList;
use App\Customer;
use App\Sender;

class ListController extends Controller
{

    public function test(){
        //session_start();
        //$id = Auth::id();
          //  $user_name = Auth::user()->name;
        //$_SESSION['editor_path'] =  '/ckfinder/'.$user_name.'-'.$id;
        //echo $_SESSION['editor_path'];
        //unset($_SESSION['editor_path']);
        //mkdir($_SERVER['DOCUMENT_ROOT'].'/ckfinder/mdir', 0741);
    }   

    public function listForm(){
        $id_user = Auth::id();
        $sender = Sender::where('user_id',$id_user)->get();
        return view('list.list-form',['data'=>$sender]);
    }

    public function addList(Request $request)
    {
    	$list = new UserList;
    	$list->user_id = Auth::id();
    	$list->name = $this->createRandomListName();
        $list->wa_number = $request->wa_number;
        $list->is_event = $request->category;
        $list->event_date = $request->date_event;
        $list->content = $request->editor1;
    	$list->save();

    	if($list->save() == true){
    		return redirect('createlist')->with('status','List has been created');
    	} else {
    		return redirect('createlist')->with('status','Error!, failed to create list');
    	}
    }

    /* User product list */
    public function userList()
    {
    	$id_user = Auth::id();
    	$userlist = UserList::where('user_id','=',$id_user)->get();
    	return view('list.list',['data'=>$userlist]);
    }

    public function userCustomer($id_list)
    {
        $customer = Customer::where('list_id','=',$id_list)->get();
        return view('list.list-customer',['data'=>$customer]);
    }

    public function displayListContent(Request $request){
        $id = $request->id;
        $list = UserList::where('id',$id)->first();

        if(is_null($list)){
            $data = null;
        } else {
            $data = array(
                'content'=> $list->content,
                'is_event'=>$list->is_event,
                'event_date'=>$list->event_date,
            );
        }
        return response()->json($data);
    }

    /* Update List content */
    public function updateListContent(Request $request){
        $id = $request->id;
        $date_event = $request->date_event;
        $editor = $request->editor;

        $lists = UserList::where('id',$id)->update([
            'event_date'=>$date_event,
            'content'=> $editor,
        ]);

        if($lists == true){
            $data['message'] = 'Data updated successfully';
        } else {
            $data['message'] = 'Error! Data failed to update';
        }
        return response()->json($data);
    }

    /* Upload image from list */
    public function uploadListImage(Request $request){
        $file = $request->file('upload');
        if($request->hasfile('upload'))
         {
            $file = $request->file('upload');
            $name=time().$file->getClientOriginalName();
            $file->move(public_path().'/assets/images/', $name);
            //$file->store('imagesupload');
            $url = url('public/assets/images/'.$name.'');
         }
         return response()->json([ 'fileName' => $name, 'uploaded' => true, 'url'=> $url]);
    }

    /* check random list name */
    public function createRandomListName(){

        $generate = $this->generateRandomListName();
        $list = Userlist::where('name','=',$generate)->first();

        if(is_null($list)){
            return $generate;
        } else {
            return $this->generateRandomListName();
        }
    }

    /* create random list name */
    public function generateRandomListName(){
        return strtolower(Str::random(8));
    }

}
