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

        if(isset($req['fields'])){
            $fields = $req['fields'];
            $filter_fields = array_unique($fields);
            $isoption = $req['isoption'];
            $addt = array_combine($fields,$isoption);
        } else {
            $fields = array();
        }

        # if isoption which mean fields otherwise dropdown
        if(isset($req['dropdown']))
        {
            $dropdown = $req['dropdown'];
            $filter_dropdown = array_unique($dropdown);
            $dropfields = $req['dropfields'];
            $drop = array_combine($dropdown,$dropfields);
        } else {
            $dropdown = array();
        }

        # validation fields
        if(isset($req['fields'])){
            foreach($fields as $ipt)
            {
                #empty fields
                if(empty($ipt))
                {
                    return redirect('createlist')->with('error_number','Error! name of fields cannot be empty');
                }

                #maximum characters
                if(strlen($ipt) > 20)
                {
                    return redirect('createlist')->with('error_number','Error! Maximum character length is 20');
                }

                # default name
                if($ipt == 'name' || $ipt == 'wa_number'){
                    return redirect('createlist')->with('error_number','Error! Sorry both of name and wa_number has set as default');
                }
            }
        }

        # fields that have same value
        if(isset($req['fields']) && isset($req['isoption']) && (count($fields) <> count($filter_fields))){
            return redirect('createlist')->with('error_number','Error! name of fields cannot be same');
        }

        # validation dropdown
        if(isset($req['dropdown'])){
            foreach($dropdown as $ipt)
            {
                #empty fields
                if(empty($ipt))
                {
                    return redirect('createlist')->with('error_number','Error! name of fields cannot be empty');
                }

                #maximum characters
                if(strlen($ipt) > 20)
                {
                    return redirect('createlist')->with('error_number','Error! Maximum character length is 20');
                }

                # default name
                if($ipt == 'name' || $ipt == 'wa_number'){
                    return redirect('createlist')->with('error_number','Error! Sorry both of name and wa_number has set as default');
                }
            }
        }

        # fields that have same value
        if(isset($req['dropdown']) && (count($dropdown) <> count($filter_dropdown))){
            return redirect('createlist')->with('error_number','Error! name of fields cannot be same');
        }

        # Filter to avoid same name both of field and dropdown
        if(isset($req['fields']) && isset($req['dropdown']))
        {
            $merge = array_merge($req['fields'],$req['dropdown']);
            $array_filter = array_unique($merge);
        }

        if(isset($req['fields']) && isset($req['dropdown']) && count($merge) <> count($array_filter))
        {
            return redirect('createlist')->with('error_number','Error! name of fields cannot be same');
        } 
        
        # Insert list to database
    	$list = new UserList;
    	$list->user_id = Auth::id();
    	$list->name = $this->createRandomListName();
        $list->wa_number = $request->wa_number;
        $list->is_event = $request->category;
        $list->label = $request->label_name;
        $list->event_date = $request->date_event;
        $list->content = $request->editor1;
        $list->pixel_text = $request->pixel_txt;
        $list->message_text = $request->message_txt;
    	$list->save();
        $listid = $list->id;

    	if($list->save() == true){
            $cfields = count($fields);
            $cdropdown = count($dropdown);
    	} else {
    		return redirect('createlist')->with('status','Error!, failed to create list');
    	}

        $success = false;
        $data = array();

        # insert fields to additional
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
            $success = null;
        }

        if($cdropdown > 0)
        {
            # insert dropdown to additional
            foreach($drop as $dropdowname=>$val)
            {
                $additional = new Additional;
                $additional->list_id = $listid;
                $additional->is_field = 1;
                $additional->name = $dropdowname;
                $additional->save();

                $parent_id = $additional->id;
                $data[$dropdowname] = $val;

                //insert dropdown option to additional
                foreach($data[$dropdowname] as $optionname)
                {
                    $additional = new Additional;
                    $additional->id_parent = $parent_id;
                    $additional->list_id = $listid;
                    $additional->name = $optionname;
                    $additional->save();
                    $success = true;
                }
            }
        } else {
            $success = null;
        }

        # if success insert all additonal
        if($success == true){
            return redirect('createlist')->with('status','Your list has been created');
        } else if($success == null) {
            return redirect('createlist')->with('status','Your list has been created');
        } else {
            return redirect('createlist')->with('error_number','Error!, failed to create list');
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

    public function customerAdditional(Request $request){
        $id = $request->id;
        $customer = Customer::where('id','=',$id)->select('additional')->first();

        if(is_null($customer))
        {
            $data['message'] = 'No Data available';
        } else {
            $data_additonal = json_decode($customer->additional,true); 
            $data['additonal'] = $data_additonal;
        }
        return response()->json($data);
    }

    public function displayListContent(Request $request){
        $id = $request->id;
        $list = UserList::where('id',$id)->first();

        if(is_null($list)){
            $data = null;
        } else {
            $additional = Additional::where('list_id',$id)->get();
            $data = array(
                'list_label'=>$list->label,
                'list_name'=>$list->name,
                'content'=> $list->content,
                'is_event'=>$list->is_event,
                'event_date'=>$list->event_date,
                'pixel'=>$list->pixel_text,
                'message'=>$list->message_text,
                'additional'=>$additional
            );
        }
        return response()->json($data);
    }

    #display field after update
    public function displayAjaxAdditional(Request $request){
        $id = $request->id;
        $additional = Additional::where('list_id',$id)->get();
        if($additional->count() > 0)
        {
            $data['additional'] = $additional;
        } else {
            $data['additional'] = array();
        }

        return response()->json($data);
    }

    #display field after update
    public function editDropfields(Request $request){
        $parent_id = $request->id;
        $additional = Additional::where('id_parent',$parent_id)->get();
        if($additional->count() > 0)
        {
            $data['dropfields'] = $additional;
        } else {
            $data['dropfields'] = array();
        }

        return response()->json($data);
    }

    public function insertFields(Request $request)
    {
        $fields = $request->fields;
        $is_option = $request->is_option;
        $list_id = $request->field_list;

        if($fields !== null && $is_option !== null)
        {
            $data = array_combine($fields,$is_option);
            foreach($data as $name => $isoption)
            {
                $additional = new Additional;
                $additional->list_id = $list_id;
                $additional->name = $name;
                $additional->is_optional = $isoption;
                $additional->save();
            }

            if($additional->save() == true)
            {
                $data['error'] = false;
                $data['msg'] = 'Your field has been added!';
                $data['listid'] = $list_id;
            }
            else
            {
                $data['error'] = true;
                $data['msg'] = 'There is error, unable to add field!';
            }
        }
        else
        {
             $data['msg'] = 'Please create at least 1 field';
        }
        return response()->json($data);
    }

    public function insertDropdown(Request $request)
    {
        $dropdowname = $request->dropdowname;
        $options = $request->doptions;
        $list_id = $request->dropdownlist;
        $uccess = false;

        if($request->dropdowname !== null)
        {
            $additional = new Additional;
            $additional->list_id = $list_id;
            $additional->is_field = 1;
            $additional->name = $dropdowname;
            $additional->save();
            $parent_id = $additional->id;
        } 
        else
        {
            $data['msg'] = 'Dropdown name cannot be empty';
            return response()->json($data);
        }

        if($additional->save() == true)
        {
            $count = count($options);
        }
        else 
        {
            $data['msg'] = 'Error!! Unable to create dropdown';
            return response()->json($data);
        }

        if($count > 0)
        {
            foreach($options as $name)
            {
                $childs = new Additional;
                $childs->id_parent = $parent_id;
                $childs->list_id = $list_id;
                $childs->name = $name;
                $childs->save();
            }
        }
        else
        {
            $data['msg'] = 'Dropdown created successfully';
        }

        if($childs->save() == true)
        {
            $data['msg'] = 'Dropdown created successfully';
        } 
        else
        {
            $data['msg'] = 'Error 001 - Unable to make option';
        }
        $data['listid'] = $list_id;
        return response()->json($data);
    }

    public function insertOptions(Request $request)
    {
        $parent_id = $request->parent_id;
        $list_id = $request->list_id;
        $success = false;

        #combine id and value from existing option
        if($request->editid !== null && $request->values !== null)
        {
            $dataedit = array_combine($request->editid,$request->values);
        }
        else 
        {
            $dataedit = null;
        }

        #insert new option
        if($request->data !== null && count($request->data) > 0)
        {
            foreach($request->data as $row)
            {
                $additional = new Additional;
                $additional->id_parent = $parent_id;
                $additional->list_id = $list_id;
                $additional->name = $row;
                $additional->save();
            }

            if($additional->save() == true)
            {
                $success = true;
            }
        }
        #data edit
        else if($dataedit !== null && count($dataedit) > 0)
        {
            foreach($dataedit as $id=>$values)
            {
                $additionaldropdown = Additional::where([['id',$id]])->update(['name'=>$values]);
            }

            if($additionaldropdown == true)
            {
                $success = true;
            }
        }
        else
        {
            $data['msg'] = 'Please create option first!';
            return response()->json($data);
        }

        if($success == true)
        {
            $data['msg'] = 'Your option menu has been added!';
            $data['listid'] = $list_id;
        } 
        else
        {
            $data['msg'] = 'Error! Sorry there is trouble with our system';
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
        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $additional = null;
        $additionaldropdown = null;

        $lists = UserList::where('id',$id)->update([
            'event_date'=>$date_event,
            'content'=> $editor,
            'pixel_text'=> $pixel,
            'message_text'=> $message,
        ]);


        if($fields !== null)
        {
            foreach($fields as $row)
            {
                $additional = Additional::where([['list_id',$id],['id',$row['idfield']]])->update(['name'=>$row['field'], 'is_optional'=>$row['isoption']]);
            }
        } 

        if($dropfields !== null)
        {
            foreach($dropfields as $col)
            {
                $additionaldropdown = Additional::where([['list_id',$id],['id',$col['idfield']]])->update(['name'=>$col['field']]);
            }
        }

        ##

        $data['listid'] = $id;
        
        if($lists == true || $additional == true || $additionaldropdown == true || $additional == null || $additionaldropdown == null)
        {
            $data['message'] = 'Data updated successfully';
        } 
        else if($additional == false)
        {
            $data['message'] = 'Error! Unable to update field';
        } 
        else if($additionaldropdown == false)
        {
            $data['message'] = 'Error! Unable to update dropdown field';
        }
        else 
        {
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

    public function delField(Request $request)
    {
        $id = $request->id;
        $list_id = $request->list_id;
        $deladditional = false;

        $additional = Additional::where([['id',$id],['list_id',$list_id]]);

        $addtional_dropdown = Additional::where([['list_id',$list_id],['id_parent',$id]]);

        if(!is_null($additional->first()))
        {
            $deladditional = $additional->delete();
        } 

        if($addtional_dropdown->count() > 0)
        {
            $deladditionaldropdown = $addtional_dropdown->delete();
        } 

        if($deladditional == true){
            $data['listid'] = $list_id;
            $data['msg'] = 'Field successfully deleted';
        } else {
            $data['msg'] = 'ID not available to delete';
        }
        return response()->json($data);
    }

    public function updateField(Request $request)
    {
        $data['error'] = true;
        $req = $request->all();

        if(count($req['is_option']) > 0)
        {
            $cfield = array_combine($req['field'], $req['is_option']);
        }

        dd($cfield);

        die('');

        $fields_array = array_column($req, 'field');
        $fields_filter = array_unique($fields_array);
        $additional = null;
        $id_addt = null;
        $dropfieldscount = 0;

        if(count($req) == 0)
        {
            $data['err'] = 'Error, you have no fields';
            return response()->json($data);
        }

        # field that have same value
        if(count($fields_array) !== count($fields_filter))
        {
            $data['err'] = 'Field value cannot be same';
            return response()->json($data);
        }

        foreach($req as $row)
        {

            echo $req['field'];
          # empty field 
          if(empty($req['field'])){
            $data['err'] = 'Field cannot be empty';
            return response()->json($data);
          }

          # maximum character length
          if(strlen($row['field']) > 20){
            $data['err'] = 'Maximum character length is 20';
            return response()->json($data);
          }

          # default value
          if($req['field'] == 'name' || $req['field'] == 'wa_number'){
            $data['err'] = 'Sorry both of name and wa_number has set as default';
            return response()->json($data);
          }

          #fields
          if(isset($req['is_option']) && isset($req['id'])) 
          {
             $additional = Additional::where([['list_id',$req['listid']],['id',$req['id']]])->update(['name'=>$req['field'], 'is_optional'=>$req['is_option']]);
          } 
          else
          {
             $additionaldropdown = Additional::where([['list_id',$req['listid']],['id',$req['id']]])->update(['name'=>$req['field']]);
          } 
          $listid = $req['listid'];

        }/* end foreach */

        if($additional == true || $additionaldropdown == true){
            $data['error'] = false;
            $data['listid'] =  $listid;
            $data['msg'] = 'Your fields updated succesfully';
        } else {
            $data['msg'] = 'Error, sorry unable to update your fields';
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
