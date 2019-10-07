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
use App\Additional;
use DB;

class ListController extends Controller
{

    public function test(){
        return $this->generateRandomListName();
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
        $req = $request->all();
        if(isset($req['fields']) && isset($req['isoption'])){
            $fields = $req['fields'];
            $isoption = $req['isoption'];
            $filter_fields = array_unique($fields);
            $addt = array_combine($fields,$isoption);
        } else {
            $fields = array();
        }

         if(isset($req['fields']) && isset($req['isoption']) && (count($fields) <> count($filter_fields))){
            return redirect('createlist')->with('status','Error! name of fields cannot be same');
         }
        
    	$list = new UserList;
    	$list->user_id = Auth::id();
    	$list->name = $this->createRandomListName();
        $list->wa_number = $request->wa_number;
        $list->is_event = $request->category;
        $list->event_date = $request->date_event;
        $list->content = $request->editor1;
        $list->pixel_text = $request->pixel_txt;
        $list->message_text = $request->message_txt;
    	$list->save();
        $listid = $list->id;

    	if($list->save() == true){
            $cfields = count($fields);
    	} else {
    		return redirect('createlist')->with('status','Error!, failed to create list');
    	}

        $success = false;
        if($cfields > 0){
            foreach($addt as $field_name=>$is_option){
                $additional = new Additional;
                $additional->list_id = $listid;
                $additional->name = $field_name;
                $additional->is_optional = $is_option;
                $additional->save();
                $success = true;
            }
        } else {
            return redirect('createlist')->with('status','Your list has been created');
        }

        if($success == true){
            return redirect('createlist')->with('status','Your list has been created');
        } else {
            return redirect('createlist')->with('status','Error!, failed to create list');
        }

    }

    /* User product list */
    public function userList()
    {
    	$id_user = Auth::id();
        $countsubscriber = DB::table('lists')
                    ->where('lists.user_id','=',$id_user)
                    ->join('customers','lists.id','=','customers.list_id')
                    ->select(DB::raw('COUNT(customers.list_id) AS totalsubscriber'))
                    ->groupBy('customers.list_id')
                    ->get();
        
        $userlist = Userlist::where('user_id',$id_user)->get();

        if($userlist->count() > 0)
        {
             foreach($userlist as $row){
                 $total = DB::table('customers')->where('list_id','=',$row->id)->select(DB::raw('COUNT(list_id) AS totalsubscriber'))->groupBy('list_id')->first();

                 if(is_null($total)){
                    $total_subs = 0;
                 } else {
                    $total_subs = $total->totalsubscriber;
                 }
                $data[] = array($row,$total_subs);
            }
        } else {
            $data = null;
        }

        //[0] = list data
        //[1] = total subscriber on each list

    	return view('list.list',['data'=>$data]);
    }

    public function total_count($idlist){
        $cst = DB::table('customers')->where('list_id','=',$idlist)->select(DB::raw('COUNT(list_id) AS totalsubscriber'))->groupBy('list_id')->first();

    }

    public function userCustomer($id_list)
    {
        $customer = Customer::where('list_id','=',$id_list)->get();
        $additional = Additional::where('list_id','=',$id_list)->get();
        return view('list.list-customer',['data'=>$customer,'additional'=>$additional]);
    }

    public function displayListContent(Request $request){
        $id = $request->id;
        $list = UserList::where('id',$id)->first();

        if(is_null($list)){
            $data = null;
        } else {
            $data = array(
                'list_name'=>$list->name,
                'content'=> $list->content,
                'is_event'=>$list->is_event,
                'event_date'=>$list->event_date,
                'pixel'=>$list->pixel_text,
                'message'=>$list->message_text
            );
        }
        return response()->json($data);
    }

    /* Update List content */
    public function updateListContent(Request $request){
        $id = $request->id;
        $date_event = $request->date_event;
        $editor = $request->editor;
        $pixel = $request->pixel;
        $message = $request->message;

        $lists = UserList::where('id',$id)->update([
            'event_date'=>$date_event,
            'content'=> $editor,
            'pixel_text'=> $pixel,
            'message_text'=> $message,
        ]);

        if($lists == true){
            $data['message'] = 'Data updated successfully';
        } else {
            $data['message'] = 'Error! Data failed to update';
        }
        return response()->json($data);
    }

    public function delListContent(Request $request)
    {
        $id = $request->id;
        $delete_userlist = UserList::where('id',$id)->delete();

        if($delete_userlist == true){
            $delete = Customer::where('list_id','=',$id)->delete();
        } else {
            $data['message'] = 'Sorry, cannot delete list, there is error';
        }

        if($delete == true){
            $data['message'] = 'Data deleted successfully';
        } else {
            $data['message'] = 'Sorry, cannot delete customer, there is error';
        }
        return response()->json($data);
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
        //return strtolower(Str::random(8));
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 8);
    }

}
