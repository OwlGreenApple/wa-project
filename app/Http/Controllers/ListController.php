<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\UserList;
use App\Customer;

class ListController extends Controller
{
    public function addList(Request $request)
    {
    	$list = new UserList;
    	$list->user_id = Auth::id();
    	$list->name = $request->name;
        $list->content = $request->editor1;
    	$list->save();

    	if($list->save() == true){
    		return redirect('home')->with('status','List has been created');
    	} else {
    		return redirect('home')->with('status','Error!, failed to create list');
    	}
    }

    /* User product list */
    public function userList()
    {
    	$id_user = Auth::id();
    	$userlist = UserList::where('user_id','=',$id_user)->get();
    	return view('list.user-list',['data'=>$userlist]);
    }

    public function userCustomer($id_list)
    {
        $customer = Customer::where('list_id','=',$id_list)->get();
        return view('list.user-customer',['data'=>$customer]);
    }

    public function displayListContent(Request $request){
        $id = $request->id;
        $list = UserList::where('id',$id)->first();

        if(is_null($list)){
            $data = null;
        } else {
            $data = array(
                'name'=> $list->name,
                'content'=> $list->content,
            );
        }
        return response()->json($data);
    }

    /* Update List content */
    public function updateListContent(Request $request){
        $id = $request->id;
        $list_name = $request->name;
        $editor = $request->editor;

        $lists = UserList::where('id',$id)->update([
            'name'=>$list_name,
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

}
