<?php

namespace App\Http\Controllers;

//require $_SERVER['DOCUMENT_ROOT'].'/waku/assets/ckfinder/core/connector/php/vendor/autoload.php';
//require $_SERVER['DOCUMENT_ROOT'].'/waku/app/ckfinder/core/connector/php/vendor/CKSource/CKFinder/CKFinder.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ListSubscribersExport;
use App\Imports\ListSubscribersImport;
use App\UserList;
use App\Customer;
use App\Sender;
use App\Additional;
use App\Reminder;
use App\PhoneNumber;
use Carbon\Carbon;
use DB;
use Session;

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

    public function index()
    {
       return view('list.list-data');
    }

    public function displaySubscriber(Request $request)
    {
       $userid = Auth::id();
       $list_id = $request->list_id;
       $data = array();
       $customer = Customer::where([['user_id',$userid],['list_id',$list_id],['status',1]])->get();
       return view('list.list-customer',['contact'=>$customer]);
    }

    public function dataList(Request $request){
       $userid = Auth::id();
      
       $lists = UserList::where([['lists.status','=',1],['lists.user_id','=',$userid]])->orderBy('id','desc')->get();
       return view('list.list-table',['lists'=>$lists]);
    }

    public function newContact($listid){
      $userid = Auth::id();
      $newcontact = Customer::whereRaw('DATE(created_at) = DATE(CURDATE()) AND list_id = "'.$listid.'" AND user_id = '.$userid.' AND status = 1')->get();

      return $newcontact->count();
    }

    public function contactList($listid){
      $userid = Auth::id();
      $contacts = Customer::where([['status','=',1],['list_id','=',$listid],['user_id','=',$userid]])->get();
       
      return $contacts->count();
    }

    public function formList()
    {
      $userid = Auth::id();
      //USE THIS CODE LATER ON VERSION 2
      $phonenumber = PhoneNumber::where('user_id',$userid)->get();
      return view('list.list');
    }

    public function createList(Request $request)
    {
      $user = Auth::user();
      $label = $request->listname;
      $autoreply = $request->autoreply;

      $rules =  [
            'listname' => 'required|min:4|max:190',
            'groupname' => 'required|min:4|max:190',
      ];
      $message = [
        'required'=> 'Field required'
      ];

      $validator = Validator::make($request->all(),$rules,$message);
      if ($validator->fails()) {
          return redirect('list-form')->withErrors($validator);               
      }

      $phone = PhoneNumber::where('user_id',$user->id)->first();
      if (is_null($phone)) {
        return redirect('list-form')->with('error_number','Error! Please set your phone number first ');
      }
      $result = $this->checkGroupByGroupName($phone,$request->groupname);
      if ( ( $result== 0) || ( $result== "0") ){
        return redirect('list-form')->with('error_number','Error 1.1! list failed to created, please contact administrator '.$result);
      }

      $list = new UserList;
      $list->user_id = Auth::id();
      $list->name = $this->createRandomListName();
      $list->label = $label;
      $list->group_name = $request->groupname;
      //$list->phone_number_id = $phone->id;
      $list->save();
      $listid = $list->id;
      $listname = $list->name;

      $data = array(
        'userid'=>$user->id,
        'label'=>$label,
        'listid'=>$listid,
        'listname'=>$listname,
      );

      $check = UserList::where('id',$listid)->first();

      if(is_null($check)){
        return redirect('list-form')->with('error_number','Error! list failed to created, please contact administrator');
      }

      //AUTO REPLY
      if(!empty($autoreply)){
        $reminder = new Reminder;
        $reminder->user_id = $user->id;
        $reminder->list_id = $listid;
        $reminder->message = $autoreply;
        $reminder->save();
      }

      Session::flash('data',$data);
      return redirect('list-created');
    }

    public function createdList(Request $request)
    {
      if(Session::get('data') == null)
      {
          return redirect('list-form');
      }
      else {
          Session::reflash();
      }
      $userid = Session::get('data')['userid'];
      $label = Session::get('data')['label'];
      $listname = Session::get('data')['listname'];
      $id = Session::get('data')['listid'];
      $listid = encrypt($id);
      $url = env('APP_URL').$listname;

      return view('list.list-create',['label'=>$label,'listid'=>$listid,'url'=>$url,'listname'=>$listname,'id'=>$id]);
    }

    public function saveList(Request $request)
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

        // if isoption which mean fields otherwise dropdown
        if(isset($req['dropdown']) && isset($req['dropfields']))
        {
            $dropdown = $req['dropdown'];
            $filter_dropdown = array_unique($dropdown);
            $dropfields = $req['dropfields'];
            $drop = array_combine($dropdown,$dropfields);
        } else {
            $filter_dropdown = null;
            $dropdown = array();
        }

        // validation fields
        if(isset($req['fields'])){
            foreach($fields as $ipt)
            {
                //empty fields
                if(empty($ipt))
                {
                    $response['status'] = 'Error! name of fields cannot be empty';
                    return response()->json($response);
                }

                //maximum characters
                if(strlen($ipt) > 20)
                {
                    $response['status'] = 'Error! Maximum character length is 20';
                    return response()->json($response);
                }

                 // default name
                if($ipt == 'subscribername' || $ipt == 'email' || $ipt == 'phone' || $$ipt == 'usertel'){
                   $response['status'] = 'Sorry, subscribername, email, phone, usertel has set as default';
                    return response()->json($response);
                }

            }
        }

        // fields that have same value
        if(isset($req['fields']) && isset($req['isoption']) && (count($fields) <> count($filter_fields))){
            $response['status'] = 'Error! name of fields cannot be same';
            return response()->json($response);
        }

        // validation dropdown
        if(isset($req['dropdown'])){
            foreach($dropdown as $ipt)
            {
                //empty fields
                if(empty($ipt))
                {
                    $response['status'] = 'Error! name of fields cannot be empty';
                    return response()->json($response);
                }

                //maximum characters
                if(strlen($ipt) > 20)
                {
                    $response['status'] = 'Error! Maximum character length is 20';
                    return response()->json($response);
                }

                // default name
                if($ipt == 'subscribername' || $ipt == 'email' || $ipt == 'phone' || $$ipt == 'usertel'){
                   $response['status'] = 'Sorry, subscribername, email, phone, usertel has set as default';
                    return response()->json($response);
                }
            }
        }

        if(isset($req['dropdown']) && $filter_dropdown == null)
        {
            $response['status'] = 'Error! you must create option if create dropdown';
            return response()->json($response);
        }

        // fields that have same value
        if(isset($req['dropdown']) && (count($dropdown) <> count($filter_dropdown))){
            $response['status'] = 'Error! name of fields cannot be same';
            return response()->json($response);
        }

        // Filter to avoid same name both of field and dropdown
        if(isset($req['fields']) && isset($req['dropdown']))
        {
            $merge = array_merge($req['fields'],$req['dropdown']);
            $array_filter = array_unique($merge);
        }

        if(isset($req['fields']) && isset($req['dropdown']) && count($merge) <> count($array_filter))
        {
            $response['status'] = 'Error! name of fields cannot be same';
            return response()->json($response);
        } 
        
        //Update list to database
        $list = new UserList;
        $list->user_id = Auth::id();
        $list->name = $this->createRandomListName();
        $list->label = $req['labelname'];
        $list->phone_number_id = $req['phoneid'];
        $list->content = $req['editor'];
        $list->pixel_text = $req['pixel'];
        $list->save();
        $listid = $list->id;
        $listname = $list->name;

        if($list->save() == true){
            $cfields = count($fields);
            $cdropdown = count($dropdown);
        } else {
            $response['status'] = 'Error!, failed to create list';
            return response()->json($response);
        }

        $success = false;
        $data = array();

        // insert fields to additional
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
            // insert dropdown to additional
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

        // if success insert all additonal
        if($success == true){
            $response['link'] = env('APP_URL').$listname;
            $response['status'] = 'Your list has been created';
        } else if($success == null) {
            $response['link'] = env('APP_URL').$listname;
            $response['status'] = 'Your list has been created';
        } else {
            $response['status'] = 'Error!, failed to create list';
        }
        return response()->json($response);
    }

    //DELETE LIST
    public function delListContent(Request $request)
    {
        $id = $request->id;
        $userid = Auth::id();
        $check_userlist = UserList::where([['id',$id],['user_id',$userid]])->first();

        if(is_null($check_userlist))
        {
            $data['message'] = 'Cannot delete list, because list not available';
            return response()->json($data);
        }

        $delete_userlist = UserList::where([['id',$id],['user_id',$userid]])->delete();
        $checkafterdelete = UserList::where([['id',$id],['user_id',$userid]])->first();

        //if success delete list then delete customer / subscriber
        if(is_null($checkafterdelete)){
            $delete = Customer::where('list_id','=',$id)->delete();
            $checkdeletecustomer = Customer::where('list_id','=',$id)->get()->count();
        } else {
            $data['message'] = 'Error, Sorry, cannot delete list';
            return response()->json($data);
        }

        //if success delete customer / subscriber
        if($checkdeletecustomer == 0){
            $deladditional = Additional::where('list_id','=',$id)->delete();
            $checkdeladditional = Additional::where('list_id','=',$id)->get()->count();
        } else {
            $data['message'] = 'Error, Sorry, cannot delete customer';
            return response()->json($data);
        } 

        //if success delete list additional
        if($checkdeladditional == 0){
            $data['message'] = 'Data deleted successfully';
        } else {
            $data['message'] = 'Error, Sorry, cannot delete list addtional';
        }
        return response()->json($data);
    }

    public function searchList(Request $request){
        $listname = $request->listname;
        $userid = Auth::id();

        if(empty($listname)){
          $lists = Userlist::where('user_id',$userid)->get();
        }

        $lists = Userlist::where('label','like','%'.$listname.'%')->get();

        return view('list.list-table',['lists'=>$lists]);
    }


    //DISPLAY ADDITIONAL ON EDIT PAGE
    public function additionalList(Request $request){
        if(!empty(Session::get('data')))
        {
            Session::reflash();
        }

        $listid = $request->id;
        $additional = Additional::where('list_id',$listid)->get();
        $data['additional'] = $additional;

        return response()->json($data);
    }

    //INSERT ADDITIONAL ON EDIT PAGE
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

    /* Insert option for additional */
    public function insertOptions(Request $request)
    {
        $parent_id = $request->parent_id;
        $list_id = $request->list_id;
        $success = false;

        //combine id and value from existing option
        if($request->editid !== null && $request->values !== null)
        {
            $dataedit = array_combine($request->editid,$request->values);
        }
        else 
        {
            $dataedit = null;
        }

        //insert new option
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
        
        //data edit
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
        if(Session::get('data') <> null)
        {
            Session::reflash();
        }
       
        $userid = Auth::id();
        $id = $request->id;
        $list_label = $request->list_label;
        $editor = $request->editor;
        $pixel = $request->pixel;
        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $additional = null;
        $additionaldropdown = null;
        $data['additionalerror'] = false;

        $lists = UserList::where([['id',$id],['user_id','=',$userid]])->update([
            'label'=>$list_label,
            'content'=> $editor,
            'pixel_text'=> $pixel,
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

        //ADDITIONAL UPDATE

        $data['listid'] = $id;
        
        if($lists == true || $additional == true || $additionaldropdown == true || $additional == null || $additionaldropdown == null)
        {
            $data['message'] = 'Data updated successfully';
        } 
        else if($additional == false)
        {
            $data['additionalerror'] = true;
            $data['message'] = 'Error! Unable to update field';
        } 
        else if($additionaldropdown == false)
        {
            $data['additionalerror'] = true;
            $data['message'] = 'Error! Unable to update dropdown field';
        }
        else 
        {
            $data['additionalerror'] = true;
            $data['message'] = 'Error! Data failed to update';
        }
        return response()->json($data);
    }

    #DELETE FIELD ADDITIONAL
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

    //EDIT LIST
    public function editList($listid){
        if(empty($listid) || $listid == null){
            return redirect('lists');
        } 

        $userid = Auth::id();
        $list = UserList::where('id',$listid)->first();

        if(is_null($list)){
            return redirect('lists');
        } 

        $data = array(
            'list_label'=>$list->label,
            'list_name'=>$list->name,
            'content'=> $list->content,
            'pixel'=>$list->pixel_text,
            'listid'=>$listid
        );

        $url = env('APP_URL').$list->name; 
        $id = $listid;
        $list_id = encrypt($id);
      
       return view('list.list-edit',['data'=>$data,'label'=>$list->label,'listid'=>$list_id,'url'=>$url,'listname'=>$list->name,'id'=>$id]);
    }

    //DUPLICATE LIST
    public function duplicateList(Request $request)
    {

        $idlist = $request->id;
        $userid = Auth::id();
        $record = Userlist::where([['id',$idlist],['user_id',$userid]])->first();

        if(is_null($record))
        {
            $response['error'] = true;
            $response['message'] = 'Invalid id, please provide valid id!';
            return response()->json($response);
        }

        //make copyname
        $copy = Userlist::where('label','like','%'.$record->label.'%')->get()->count();

        if($copy <= 1)
        {
            $embed = '-copy';
        }
        else
        {
            $copy = $copy-1;
            $embed = '-copy-'.$copy;
        }

        $checknewlabellength = strlen($record->label.$embed);

        if($checknewlabellength > 50)
        {
            $response['error'] = true;
            $response['message'] = 'List name characters cannot more than 50 characters';
            return response()->json($response); 
        }

        $list = new UserList;
        $list->user_id = Auth::id();
        $list->name = $this->createRandomListName();
        $list->label = $record->label.$embed;
        $list->content = $record->content;
        $list->pixel_text = $record->pixel_text;
        $list->save();
        $newIdList = $list->id; //new list id
        $opt = array();

        //IF LIST DUPLICATED SUCCESSFULLY
        if($list->save() == true)
        {
            $checkadditional = Additional::where('list_id',$idlist)->get();
            //SORT ADDITIONAL BASED ON ID PARENT
            $recordadditional = Additional::where('list_id',$idlist)->orderByRaw('CASE WHEN id_parent = 0 THEN id ELSE id_parent END ASC, CASE WHEN id_parent = 0 THEN 0 ELSE id END ASC')->get();
        }
        else
        {
            $response['error'] = true;
            $response['message'] = 'Error, Sorry unable to duplicate list record';
            return response()->json($response);
        } 

        //IF ADDITIONAL AVAILABLE
        if($checkadditional->count() > 0)
        {
            foreach($recordadditional as $rows)
            {
                if($rows->id_parent == 0)
                {
                    $additional = new Additional;
                    $additional->id_parent = $rows->id_parent;
                    $additional->list_id = $newIdList;
                    $additional->is_field = $rows->is_field;
                    $additional->name = $rows->name;
                    $additional->is_optional = $rows->is_optional;
                    $additional->save();
                }
                else
                {
                    $newparentid = $additional->id;
                    $opt[$newparentid][] = $rows->name;
                }
            } 
        }
        else
        {
            $response['error'] = false;
            $response['message'] = 'List successfully duplicated!';
            return response()->json($response);
        }
        
      
        //IF DROPDOWN HAS OPTIONS
        if(count($opt) > 0)
        {
            foreach($opt as $newparentid=>$cols)
            {
                foreach($cols as $name)
                {
                    $additional = new Additional;
                    $additional->id_parent = $newparentid;
                    $additional->list_id = $newIdList;
                    $additional->is_field = 0;
                    $additional->name = $name;
                    $additional->is_optional = 0;
                    $additional->save();
                }
            }
        }
        else
        {
            $response['error'] = false;
            $response['message'] = 'List successfully duplicated!';
            return response()->json($response);
        }

        //IF ADDITIONAL SAVED SUCCESSFULLY
        if($additional->save() == true)
        {
            $reminderList = Reminder::where([['list_id',$idlist],['user_id',$userid]])->get();
            $response['error'] = false;
            $response['message'] = 'List successfully duplicated!';
        }
        else
        {
            $response['error'] = true;
            $response['message'] = 'Error, Sorry unable to duplicate list record';
        }

        return response()->json($response);

        /*
        // DUPLICATE ENTIRE LIST REMINDER
        if($reminderList->count() > 0)
        {
            foreach($reminderList as $cols)
            {
                $reminder = new Reminder;
                $reminder->user_id = Auth::id();
                $reminder->list_id = $newIdList;
                $reminder->days = $cols->days;
                $reminder->hour_time = $cols->hour_time;
                $reminder->message = $cols->message;
                $reminder->save();
            }
        }
        else
        {
            $response['error'] = false;
            $response['message'] = 'List successfully duplicated!';
            return response()->json($response);
        }

        //IF DUPLICATE REMINDER SUCCESSFULLY
        if($reminder->save() == true)
        {
            $response['error'] = false;
            $response['message'] = 'List successfully duplicated!';
        }
        else
        {
            $response['error'] = true;
            $response['message'] = 'Error, Sorry unable to duplicate list record';
        }
        */
        return response()->json($response);
    }

    //IMPORT SUBSCRIBER / CUSTOMER INTO CSV
    function importCSVListSubscribers(Request $request)
    {
        $id_list = $request->list_id_import;
        $userid = Auth::id();

        $check = UserList::where([['id',$id_list],['user_id',$userid]])->first();
        if(is_null($check))
        {
            $msg['message'] = 'Invalid List!';
            return response()->json($msg);
        }

        $phone = PhoneNumber::where('user_id',$userid)->first();
        if (is_null($phone)) {
           return response()->json(['message'=>'Error! Please set your phone number first']);
        }

        $file = $request->file('csv_file');
        $import = new ListSubscribersImport($id_list);
        Excel::import($import, $file);

        if($import->getRowCount() > 0)
        {
            $msg['message'] = 'Import Successful';
        }
        else
        {
            $msg['message'] = 'Import Failed';
        }
        return response()->json($msg);
    }

    public function getPhoneTelegramChatID($listid,$subscriber_phone_number) {
      $curl = curl_init();

      $list = UserList::where('id',$listid)->first();
      $phoneNumber = PhoneNumber::find($list->phone_number_id);

      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phoneNumber->phone_number,
          'groupname'=>$list->group_name, 
          'filename'=>env('FILENAME_API').$phoneNumber->id,
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/getChatId-phone.php",
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_POST => 1,
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
        // echo $response."\n";
        $result = json_decode($response,true);
        //dd($result);
        $chat_id = 0;
        foreach($result as $res){
          if ($res["phone_number"]==$subscriber_phone_number) {
            $chat_id = $res["id"];
          }
        }
        if ($chat_id == 0){
          $data['success'] = false;
          $data['message'] = 'Error-000! Sorry there is something wrong with our system';
          return response()->json($data);
        }

        return $chat_id;
        //$customer->chat_id = $chat_id;
      }
    }

    public function getChatIDByUsername($phoneNumber,$usertel){
      /*
      * Write to PHPTDLIB API Server 
      * (Username Telegram)
      */
      $curl = curl_init();
      

      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phoneNumber->phone_number,
          'username'=>$usertel, 
          'filename'=>env('FILENAME_API').$phoneNumber->id,
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/getChatId-username.php",
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_POST => 1,
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        // echo "cURL Error #:" . $err;
        return "cURL Error #:" . $err;
      } else {
        // echo $response."\n";
        //$customer->chat_id = $response;
        return $response;
      }
    }

    public function checkGroupByGroupName($phoneNumber,$groupName){
      /*
      * Write to PHPTDLIB API Server 
      * (Username Telegram)
      */
      $curl = curl_init();
      

      $data = array(
          'token'=> env('TOKEN_API'),
          'phone_number' => $phoneNumber->phone_number,
          'username'=>$groupName, 
          'filename'=>env('FILENAME_API').$phoneNumber->id,
      );

      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://172.98.193.36/phptdlib/php_examples/check-group.php",
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_POST => 1,
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      if ($err) {
        // echo "cURL Error #:" . $err;
        return "cURL Error #:" . $err;
      } else {
        // echo $response."\n";
        //$customer->chat_id = $response;
        return $response;
      }
    }


    /* *************************************** 
        OLD CODES
     *************************************** */
    public function listForm(){
        return view('list.list-form');
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

        // if isoption which mean fields otherwise dropdown
        if(isset($req['dropdown']) && isset($req['dropfields']))
        {
            $dropdown = $req['dropdown'];
            $filter_dropdown = array_unique($dropdown);
            $dropfields = $req['dropfields'];
            $drop = array_combine($dropdown,$dropfields);
        } else {
            $filter_dropdown = null;
            $dropdown = array();
        }

        // validation fields
        if(isset($req['fields'])){
            foreach($fields as $ipt)
            {
                //empty fields
                if(empty($ipt))
                {
                    return redirect('createlist')->with('error_number','Error! name of fields cannot be empty');
                }

                //maximum characters
                if(strlen($ipt) > 20)
                {
                    return redirect('createlist')->with('error_number','Error! Maximum character length is 20');
                }

                // default name
                if($ipt == 'name' || $ipt == 'bot_api'){
                    return redirect('createlist')->with('error_number','Error! Sorry both of name and bot_api has set as default');
                }
            }
        }

        // fields that have same value
        if(isset($req['fields']) && isset($req['isoption']) && (count($fields) <> count($filter_fields))){
            return redirect('createlist')->with('error_number','Error! name of fields cannot be same');
        }

        // validation dropdown
        if(isset($req['dropdown'])){
            foreach($dropdown as $ipt)
            {
                //empty fields
                if(empty($ipt))
                {
                    return redirect('createlist')->with('error_number','Error! name of fields cannot be empty');
                }

                //maximum characters
                if(strlen($ipt) > 20)
                {
                    return redirect('createlist')->with('error_number','Error! Maximum character length is 20');
                }

                // default name
                if($ipt == 'name' || $ipt == 'bot_api'){
                    return redirect('createlist')->with('error_number','Error! Sorry both of name and bot_api has set as default');
                }
            }
        }

        if(isset($req['dropdown']) && $filter_dropdown == null)
        {
             return redirect('createlist')->with('error_number','Error! you must create option if create dropdown');
        }

        // fields that have same value
        if(isset($req['dropdown']) && (count($dropdown) <> count($filter_dropdown))){
            return redirect('createlist')->with('error_number','Error! name of fields cannot be same');
        }

        // Filter to avoid same name both of field and dropdown
        if(isset($req['fields']) && isset($req['dropdown']))
        {
            $merge = array_merge($req['fields'],$req['dropdown']);
            $array_filter = array_unique($merge);
        }

        if(isset($req['fields']) && isset($req['dropdown']) && count($merge) <> count($array_filter))
        {
            return redirect('createlist')->with('error_number','Error! name of fields cannot be same');
        } 
        
        //Insert list to database
        $list = new UserList;
        $list->user_id = Auth::id();
        $list->name = $this->createRandomListName();
        $list->label = $request->label_name;
        $list->content = $request->editor1;
        $list->pixel_text = $request->pixel_txt;
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

        // insert fields to additional
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
            // insert dropdown to additional
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

        // if success insert all additonal
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
        return view('list.list-customer',['data'=>$customer,'additional'=>$additional,'listid'=>$id_list]);
    }

    #EXPORT SUBSCRIBER / CUSTOMER INTO CSV
    public function exportListCSVSubscriber($id_list){
        $id_user = Auth::id();
        $customer = Customer::where([['list_id',$id_list],['user_id','=',$id_user]])->get();
        $listname = Userlist::where([['id',$id_list],['user_id',$id_user]])->first();
        $today = Date('d-m-Y');
       
        if(empty($id_list) || empty($id_user) || $customer->count() <= 0 || is_null($listname)){
            return redirect('userlist');
        }
        $filename = 'subscriber-'.$listname->label.'.csv';
        return (new ListSubscribersExport($id_list))->download($filename);
    }

    #CUSTOMER ADDITIONAL INPUT
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

    
    /* NOT USED ANYMORE */
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
