<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;
// use App\Exports\ListSubscribersExport;
// use App\Imports\ListSubscribersImport;
use App\Rules\ImportValidation;
use App\External\ExcelValueBinder;
use App\UserList;
use App\Customer;
use App\Sender;
use App\Additional;
use App\Reminder;
use App\ReminderCustomers;
use App\BroadCast;
use App\BroadCastCustomers;
use App\PhoneNumber;
use Carbon\Carbon;
use DB;
use Session;
use stdClass;

class ListController extends Controller
{

    public function test(){
        // return $this->generateRandomListName();
        //session_start();
        //$id = Auth::id();
          //  $user_name = Auth::user()->name;
        //$_SESSION['editor_path'] =  '/ckfinder/'.$user_name.'-'.$id;
        //echo $_SESSION['editor_path'];
        //unset($_SESSION['editor_path']);
        //mkdir($_SERVER['DOCUMENT_ROOT'].'/ckfinder/mdir', 0741);
         $order = new stdClass();
        $order->no_order = 'Test'; 
        return view('order.thankyou',['order'=>$order]);
    }   

    public function index(Request $request)
    {
      $userid = Auth::id();
      $paging = 25;
      $lists = UserList::where([['lists.status','=',1],['lists.user_id','=',$userid]])->orderBy('id','desc')->paginate($paging);

      if($request->ajax()) {
          return view('list.list-table',['lists'=>$lists,'paginate'=>$lists,'listcontroller'=> new ListController]);
      }

      return view('list.list-data',['lists'=>$lists,'paginate'=>$lists,'listcontroller'=> new ListController]);
    }

    public function ListContacts($list_id)
    {
       $userid = Auth::id();
       $lists = UserList::where([['id',$list_id],['user_id',$userid]])->first();
       if(is_null($lists))
       {
          return redirect('lists');
       }

       $customer = Customer::where([['user_id',$userid],['list_id',$list_id],['status',1]])->get();
       return view('list.list-customer',['contact'=>$customer,'label'=>$lists->label]);
    }

    public function dataList(Request $request){
       $userid = Auth::id();
       $lists = UserList::where([['status','=',1],['user_id','=',$userid]])->orderBy('id','desc')->get();
       return view('list.list-table',['lists'=>$lists,'listcontroller'=> new ListController]);
    }

    public function newContact($listid){
      $userid = Auth::id();
      $newcontact = Customer::where('status','>',0)->whereRaw('DATE(created_at) = DATE(CURDATE()) AND list_id = "'.$listid.'" AND user_id = '.$userid.'')->get();

      return $newcontact->count();
    }

    public function contactList($listid){
      $userid = Auth::id();
      $contacts = Customer::where([['status','>',0],['list_id','=',$listid],['user_id','=',$userid]])->get();
       
      return $contacts->count();
    }

    public function formList()
    {
      $userid = Auth::id();
      //USE THIS CODE LATER ON VERSION 2
      $phonenumber = PhoneNumber::where('user_id',$userid)->get();
      return view('list.list');
    }

    public function save_auto_reply(Request $request)
    {
			$user = Auth::user();
			
      // dd($request->all());

			$list = UserList::find($request->idlist);
			if (!is_null($list)) {
				if ($list->user_id<>$user->id) {
					//not authorize
					return response()->json([
						"status"=>"error",
						"message"=>"not authorize",
					]);
				}
			}
			
      // pengecekan error nya klo ga ada [START] [UNSUBS] [REPLY_CHAT]
      if ($request->is_secure) 
      {
        // if (strpos($request->autoreply, '[REPLY_CHAT]') == false) {
					// return response()->json([
						// "status"=>"error",
						// "message"=>"Error! String must be contain [REPLY_CHAT] ",
					// ]);
				// }
        if (strpos($request->autoreply, '[START]') == false) {
					return response()->json([
						"status"=>"error",
						"message"=>"Error! String must be contain [START] ",
					]);
        }
        if (strpos($request->autoreply, '[UNSUBS]') == false) {
					return response()->json([
						"status"=>"error",
						"message"=>"Error! String must be contain [UNSUBS] ",
					]);
        }
      }
			
			$reminder = Reminder::where('user_id',$user->id)
										->where('list_id',$request->idlist)
										->where('days',0)
										->where('is_event',0)
                    ->first();
										
      //AUTO REPLY
      if(!is_null($reminder))
      {
        //UPDATE
        $reminder->message = $request->autoreply;
        if ($request->is_secure) {
          $reminder->status = 0;
        }

        try
        {
           $reminder->save();
        }
        catch(QueryException $e)
        {
          //$e->getMessage();
          return response()->json([
            "status"=>"error",
            "message"=>'Sorry, currently our system is too busy',
          ]);
        }
      }
      else
      {
        // INSERT
        $new_reminder = new Reminder;
        $new_reminder->user_id = $user->id;
        $new_reminder->list_id = $request->idlist;
        $new_reminder->message = $request->autoreply;
        $new_reminder->days = 0;
        $new_reminder->is_event = 0;

        try
        {
           $new_reminder->save();
        }
        catch(QueryException $e)
        {
          //$e->getMessage();
          return response()->json([
            "status"=>"error",
            "message"=>'Sorry, currently our system is too busy',
          ]);
        }
      }
			
			if (!is_null($list)) {
				$list->is_secure = $request->is_secure;
				$list->start_custom_message = $request->start_custom_message;
				$list->unsubs_custom_message = $request->unsubs_custom_message;
				$list->save();
			}
			
			return response()->json([
				"status"=>"success",
				"message"=>"Your auto reply message has saved!",
			]);
		}
		
    public function createList(Request $request)
    {
      $user = Auth::user();
      $label = $request->listname;
      $autoreply = $request->autoreply;

      $rules =  [
            'listname' => 'required|min:4|max:190',
            'autoreply' => 'max:65500',
      ];
      $message = [
        'required'=> 'Field required'
      ];

      $validator = Validator::make($request->all(),$rules,$message);
      if ($validator->fails()) {
          return redirect('list-form')->withErrors($validator)->with('listname',$request->listname)
          ->with('listname',$request->listname)
          ->with('autoreply',$request->autoreply)
					->with('start_custom_message',$request->start_custom_message)
					->with('unsubs_custom_message',$request->unsubs_custom_message)
          ;               
      }

      $phone = PhoneNumber::where('user_id',$user->id)->first();
      if (is_null($phone)) 
      {
        return redirect('list-form')->with('error_number','Error! Please set your phone number first ')
          ->with('listname',$request->listname)
          ->with('autoreply',$request->autoreply)
					->with('start_custom_message',$request->start_custom_message)
					->with('unsubs_custom_message',$request->unsubs_custom_message)
          ;
      }
      
      // pengecekan error nya klo ga ada [START] [UNSUBS] [REPLY_CHAT]
      if ($request->is_secure) {
        // if (strpos($request->autoreply, '[REPLY_CHAT]') == false) {
          // return redirect('list-form')->with('error_number','Error! String must be contain [REPLY_CHAT] ')
            // ->with('listname',$request->listname)
            // ->with('autoreply',$request->autoreply)
            // ;
        // }
        if (strpos($request->autoreply, '[START]') == false) {
          return redirect('list-form')->with('error_number','Error! String must be contain [START] ')
            ->with('listname',$request->listname)
            ->with('autoreply',$request->autoreply)
            ->with('start_custom_message',$request->start_custom_message)
            ->with('unsubs_custom_message',$request->unsubs_custom_message)
            ;
        }
        if (strpos($request->autoreply, '[UNSUBS]') == false) {
          return redirect('list-form')->with('error_number','Error! String must be contain [UNSUBS] ')
            ->with('listname',$request->listname)
            ->with('autoreply',$request->autoreply)
            ->with('start_custom_message',$request->start_custom_message)
            ->with('unsubs_custom_message',$request->unsubs_custom_message)
            ;
        }
      }

      $list = new UserList;
      $list->user_id = Auth::id();
      $list->name = $this->createRandomListName();
      $list->label = $label;
      $list->phone_number_id = $phone->id;
      $list->is_secure = $request->is_secure;
      $list->start_custom_message = $request->start_custom_message;
      $list->unsubs_custom_message = $request->unsubs_custom_message;
      $list->save();
      $listid = $list->id;
      $listname = $list->name;

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
        if ($request->is_secure) {
          $reminder->status = 0;
        }
        $reminder->save();
      }

      return redirect('list-edit/'.$listid.'');
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

        $delete_userlist = UserList::find($id);
        $delete_userlist->status = 0;
        $delete_userlist->save();

        //if success delete list then delete customer / subscriber
        try{
            $delete = Customer::where('list_id','=',$id)->update(['status'=>0]);
        } catch(Exception $e) {
            $data['message'] = 'Error, Sorry, cannot delete list';
            return response()->json($data);
        }

        //if success delete customer / subscriber
       /* try{
            $deladditional = Additional::where('list_id','=',$id)->delete();
        } catch(Exception $e) {
            $data['message'] = 'Error, Sorry, cannot delete customer';
            return response()->json($data);
        } */

        //if success delete list additional
        if($delete){
            $data['message'] = 'List deleted successfully';
        } else {
            $data['message'] = 'Error, Sorry, cannot delete list addtional';
        }
        return response()->json($data);
    }

    public function searchList(Request $request)
    {
        $listname = $request->listname;
        $userid = Auth::id();

        if($listname == null)
        {
          $lists = Userlist::where([['user_id',$userid],['status','>',0]])->get();
        }
        else
        {
          $lists = Userlist::where([['user_id',$userid],['status','>',0],['label','like','%'.$listname.'%']])->get();
        }
        
        return view('list.list-table',['lists'=>$lists,'paginate'=>null,'listcontroller'=> new ListController]);
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
        $userid = Auth::id();
        $id = $request->id;

        //$list_label = $request->list_label;
        $label_name = $request->label_name;
        $label_phone = $request->label_phone;
        $label_email = $request->label_email;
        $editor = $request->editor;
        $pixel = $request->pixel;
        $fields = $request->fields;
        $dropfields = $request->dropfields;
        $additional = null;
        $additionaldropdown = null;
        $data['additionalerror'] = false;

        // $lists = UserList::where([['id',$id],['user_id','=',$userid]])->update([
        $lists = UserList::find($id);
        $lists->label_name = $label_name;
        $lists->label_last_name = $request->label_last_name;
        $lists->label_phone = $label_phone;
        $lists->label_email = $label_email;
        $lists->checkbox_email = $request->checkbox_email;
        $lists->checkbox_lastname = $request->checkbox_lastname;
        $lists->button_subscriber = $request->button_rename;
        $lists->message_conf = $request->conf_message;
        $lists->content = $editor;
        $lists->pixel_text = $pixel;

        try
        {
          $lists->save();
        }
        catch(Exception $e)
        {
           $data['message'] = 'Sorry, unable to update your list, our system is busy';
           return response()->json($data);
        }

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

    public function changeListName(Request $request)
    {
        $userid = Auth::id();
        $id = $request->id;
        $list_label = $request->list_name;
        $result['status'] = false;

        if(empty($list_label) || $list_label == null)
        {
            $result['status'] = 'error';
            $result['response'] = 'List name cannot be empty';
            return response()->json($result);
        } 

        if(strlen($list_label) > 100)
        {
            $result['status'] = 'error';
            $result['response'] = 'List name cannot greater than 100 characters';
            return response()->json($result);
        }

        try{
            $lists = UserList::where([['id',$id],['user_id','=',$userid]])->update([
                'label'=>$list_label,
            ]);
            $result['status'] = 'success';
            $result['response'] = 'List name has been updated';
        }
        catch(Exception $e)
        {
            $result['response'] = 'Error, failed to change list name';
        }
        return response()->json($result);
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
        $list = UserList::where([['id',$listid],['user_id',$userid],['status','>',0]])->first();
        if(is_null($list)){
            return redirect('lists');
        } 

        $mod = request()->get('mod');

				$auto_reply_message = "";
        $data_autoreply = array();
				$reminder = Reminder::where("list_id",$listid)
										->where("campaign_id",0)
										->where("days",0)
										->where("user_id",$userid)
										->first();
				if (!is_null($reminder)) {
					$auto_reply_message = $reminder->message;
          $id_auto_reply = $reminder->id;

          $data_autoreply = ReminderCustomers::where([['reminder_customers.reminder_id','=',$id_auto_reply],['reminder_customers.user_id',$userid]])
            ->leftJoin('customers','reminder_customers.customer_id','=','customers.id')
            ->select('reminder_customers.updated_at','reminder_customers.status','customers.name','customers.telegram_number')->get();
				}

        $data = array(
            'list_label'=>$list->label,
            'list_name'=>$list->name,
            'label_name'=>$list->label_name,
            'label_last_name'=>$list->label_last_name,
            'label_phone'=>$list->label_phone,
            'label_email'=>$list->label_email,
            'checkbox_email'=>$list->checkbox_email,
            'checkbox_lastname'=>$list->checkbox_lastname,
            'content'=> $list->content,
            'message_conf'=> $list->message_conf,
            'pixel'=>$list->pixel_text,
            'button_subscriber'=> $list->button_subscriber,
            'listid'=>$listid,
            'is_secure'=>$list->is_secure,
            'auto_reply_message'=>$auto_reply_message,
            'start_custom_message'=>$list->start_custom_message,
            'unsubs_custom_message'=>$list->unsubs_custom_message,
            'auto_reply'=>$data_autoreply,
            'mod'=>$mod
        );

        $url = env('APP_URL').$list->name; 
        $id = $listid;
        $list_id = encrypt($id);

       return view('list.list-edit',['data'=>$data,'label'=>$list->label,'listid'=>$list_id,'url'=>$url,'listname'=>$list->name,'id'=>$id]);
    }

    //DISPLAY AJAX ON PAGE LIST-EDIT TAB CONTACT.
    public function listTableCustomer(Request $request)
    {
        $userid = Auth::id();
        $listid = $request->list_id;
        $customer = Customer::where([['list_id',$listid],['user_id',$userid],['status','=',1]])->orderBy('created_at','desc')->get();
        return view('list.list-table-customer',['customer'=>$customer]);
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

    public function deleteSubscriber(Request $request)
    {
        $userid = Auth::id();
        $id_customer = $request->id_customer;
        $list_id = $request->list_id;

        if($id_customer == null)
        {
            return redirect('lists');
        }

        try
        {
          Customer::where([['id',$id_customer],['user_id',$userid]])->update(['status'=>0]);
        }
        catch(Exception $e)
        {
          $data['success'] = 0;
          $data['message'] = 'Failed to delete your customer, please try again later';
          return response()->json($data);
        }

        try
        {
          ReminderCustomers::where([['list_id',$list_id],['customer_id',$id_customer],['status','=',0]])->update(['status'=>4]);

          BroadCast::where([['broad_casts.list_id',$list_id],['broad_cast_customers.status','=',0],['broad_cast_customers.customer_id',$id_customer]])->join('broad_cast_customers','broad_cast_customers.id','=','broad_casts.id')->update(['broad_cast_customers.status'=>4]);

          $data['success'] = 1;
          $data['message'] = 'Your customer deleted successfully';
        }
        catch(Exception $e)
        {
          $data['success'] = 0;
          $data['message'] = 'Failed to delete your customer, please try again later';
        }

        return response()->json($data);
    }

    public function importExcelListSubscribers(Request $request)
    {
        $id_list = $request->list_id_import;
        $arr = array();
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

       /* $extension = $request->file('csv_file')->getClientOriginalExtension();
        if($extension <> "xlsx")
        {
          return response()->json(['message'=>'Please use .xlsx file extension only!']);
        }*/

        $file = $request->file('csv_file')->getRealPath();
        $binder = new ExcelValueBinder;
        $data = Excel::setValueBinder($binder)->load($file)->get();
        $count = 0;

        if($data->count() > 0)
        {
           foreach ($data as $key => $value) 
           {
              $phone = strval($value->phone);
              if(substr($phone,0,1) <> '+')
              {
                  $phone = '+'.$phone;
              }
              $check_phone = $this->checkAvailablePhone($id_list,$phone);

              if($check_phone == true)
              {
                  $count++;
              }
           }

           if($count > 0)
           {
              $msg['duplicate'] = 1;
           }
           else
           {
              $msg['duplicate'] = 0;
           }
        }
        else
        {
            $msg['success'] = 1;    
            $msg['message'] = 'Your file is empty, nothing to import';
        }

        return response()->json($msg);
    }

    public function checkAvailablePhone($list_id,$number)
    {
        $userid = Auth::id();
        $customer = Customer::where([['user_id',$userid],['list_id',$list_id],['telegram_number','=',$number]])->first();
       
        if(!is_null($customer))
        {
           return true;
        }
        else
        {
           return false;
        }
    }

    public function importExcelListSubscribersAct(Request $request)
    {
        $userid = Auth::id();
        $id_list = $request->list_id_import;
        $overwrite = $request->overwrite;

        if($request->file('csv_file') == null)
        {
            $err = array(
                'success'=>0,
                'message'=>'File cannot be empty'
            );
            return response()->json($err);
        }

        $file = $request->file('csv_file')->getRealPath();
        $binder = new ExcelValueBinder;
        $data = Excel::setValueBinder($binder)->load($file)->get();
        $count = 0;
        $rowcolumn = 1;

        if($data->count() > 0)
        {
            foreach ($data as $key => $value) 
            {
              $name = $value->name;
              $phone = strval($value->phone);
              $email = $value->email;
              $rowcolumn++;

              //FILTER 1
              $check_valid = $this->checkValid($name,$phone,$email,$rowcolumn);
             
              if($check_valid['error'] == 1)
              {
                  $err = array(
                      'success'=>0,
                      'name'=>$check_valid['name'],
                      'phone'=>$check_valid['phone'],
                      'email'=>$check_valid['email']
                  );
                  return response()->json($err);
              }
              
              //FILTER 2
              if(substr($phone,0,1) <> '+')
              {
                  $phone = '+'.$phone;
              }

              $checkuniquephone = $this->checkUniquePhone($phone,$id_list);           
              // $checkuniqueemail = $this->checkUniqueEmail($email,$id_list);

              if($checkuniquephone == true)
              {
                $customer = new Customer;
                $customer->user_id = $userid;
                $customer->list_id = $id_list;
                $customer->name = $name;
                $customer->telegram_number = $phone;
                $customer->email = $email;
                $customer->status = 1;
                
                try
                {     
                  $customer->save();
                  $customer::create_link_unsubs($customer->id,$id_list);
                  $count++;
                }
                catch(Exception $e)
                {
                  $msg['success'] = 0;
                  $msg['message'] = 'Failed to import,sorry there is something wrong on our server';
                  return response()->json($msg);
                }
              }

              if($overwrite == 1)
              {
                 $customer_rewrite = Customer::where([['telegram_number',$phone],['list_id',$id_list]])->first();
                 $customer_id = $customer_rewrite->id;
                 $data_customer = Customer::find($customer_id);
                 $data_customer->name = $name;
                 $data_customer->email = $email;
                 $data_customer->status = 1;
                 $data_customer->save();
              }
            } // ENDFOREACH

            if($count > 0)
            {
                $msg['success'] = 1;
                $msg['message'] = 'Import Successful';
            }
            else
            {
                $msg['success'] = 1;
                $msg['message'] = 'Nothing to import, your data on import file had available in our database';
            }
            return response()->json($msg);
        }
        else
        {
            $msg['success'] = 1;
            $msg['message'] = 'Your file is empty, nothing to import';
            return response()->json($msg);
        }
    }



    private function checkUniquePhone($number,$list_id)
    {
        $userid = Auth::id();
        $customer = Customer::where([['user_id',$userid],['list_id',$list_id],['telegram_number','=',$number]])->first();
        if(is_null($customer))
        {
           return true;
        }
        else {
           return false;
        }
    }
    
    private function checkUniqueEmail($email,$list_id)
    {
        $userid = Auth::id();
        $email = Customer::where([['user_id',$userid],['email','=',$email],['list_id',$list_id],])->first();
        if(is_null($email))
        {
           return true;
        }
        else {
           return false;
        }
    }

    private function checkValid($name,$phone,$email,$rowerror)
    {
        $data = array(
            'name'=>$name,
            'phone'=>$phone,
            'email'=>$email,
        );

        $rules = [
          'name'=> ['required','max:50'],
          'phone'=> ['required','min:10','max:22',new ImportValidation],
          'email'=>['max:190']
        ];

        $validator = Validator::make($data,$rules);

        if($validator->fails())
        {
            $errors = $validator->errors();

            $err_name = str_replace("."," ",$errors->first('name'));
            ($err_name <> null)?$error_name =  $err_name.'on row : '.$rowerror : $error_name='';

            $err_phone = str_replace("."," ",$errors->first('phone'));
            ($err_phone <> null)?$error_phone =  $err_phone.'on row : '.$rowerror : $error_phone='';

            $err_email = str_replace("."," ",$errors->first('email'));
            ($err_email <> null)?$error_email = $err_email.'on row : '.$rowerror : $error_email='';
          
            $err = array(
              'error'=>1,
              'name'=>$error_name,
              'phone'=>$error_phone,
              'email'=>$error_email,
            );
        }
        else
        {
            $err['error'] = 0;
        }

        return $err;
    }

     /* check random list name */
    public function createRandomListName(){

        $generate = $this->generateRandomListName();
        $list = Userlist::where([['name','=',$generate],['status',1]])->first();

        if(is_null($list)){
            return $generate;
        } else {
            return $this->createRandomListName();
        }
    }

    /* create random list name */
    public function generateRandomListName(){
        //return strtolower(Str::random(8));
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($permitted_chars), 0, 8);
    }

    // EXPORT SUBSCRIBER / CUSTOMER INTO CSV

    public function exportListExcelSubscriber($list_id,$import)
    {
        $userid = Auth::id();
        $check = UserList::where('id',$list_id)->first();

        if(is_null($check))
        {
            return redirect('lists');
        }

        $day = Carbon::now()->toDateString();

        if($import == 1)
        {
          $filename = 'list-'.$check->label.'-'.$day.'-for-import';
        }
        else
        {
          $filename = 'list-'.$check->label.'-'.$day.'-for-data';
        }

        $list_subscriber = Customer::query()->where([['list_id',$list_id],['user_id','=',$userid]])->select('name','telegram_number','email','additional')->get();

        $data = array(
            'import'=>$import,
            'customers'=>$list_subscriber,
        );

        Excel::create($filename, function($excel) use ($data) 
        {
          $excel->sheet('New sheet', function($sheet) use ($data) 
          {
            
              if($data['customers']->count() > 0)
              {
                if($data['import'] == 1)
                {
                  $column[0] = array();
                  foreach($data['customers'] as $row)
                  {
                      $phone = $row->telegram_number;
                      if(substr($phone,0,1) == '+')
                      {
                          $phone = str_replace("+", "", $phone);
                      }

                      $column[] = array(
                        $row->name,
                        $phone,
                        $row->email
                      );
                  }
                }
                else
                {
                  $column[0] = $column[1] = array();
                  foreach($data['customers'] as $row)
                  {
                      $phone = $row->telegram_number;
                      if(substr($phone,0,1) == '+')
                      {
                          $phone = str_replace("+", "", $phone);
                      }

                      $column[] = array(
                        $row->name,
                        $phone,
                        $row->email,
                        $this->renderAdditional($row->additional)
                      ); //end array
                  } 
                } // end else 
              }

              $sheet->fromArray($column, null, 'A1', false, false);

              if($data['import'] == 0)
              {
                $sheet->cell('A1', 'Customer Name'); 
                $sheet->cell('B1', 'WA Number'); 
                $sheet->cell('C1', 'Customer Email');
                $sheet->cell('D1', 'Additional'); 
              }
              else
              {
                $sheet->cell('A1', 'name'); 
                $sheet->cell('B1', 'phone'); 
                $sheet->cell('C1', 'email');
              } 
              
          });
        })->export('xlsx');
    }

    public function renderAdditional($addt)
    {
       $additional_result = '';
       $up = 0;

       if($addt <> null || !empty($addt))
        {
            $additonal = json_decode($addt,true);
            $totalstring = count($additonal);
            foreach($additonal as $label=>$value)
            {   
                $up++;
                $string = $label.' = '.$value;

                if($up == $totalstring)
                {
                    $additional_result .= $string;
                }
                else
                {
                    $additional_result .= $string."\n";
                }
            }
        }
        else
        {
            $additional_result = '-';
        }
        return $additional_result;
    }

    public function listForm(){
        return view('list.list-form');
    }

    public function resendAutoReply(Request $request)
    { 
        $userid = Auth::id();
        $listid = $request->list_id;

        $reminder = Reminder::where("list_id",$listid)
                    ->where("campaign_id",0)
                    ->where("days",0)
                    ->where("campaign_id",0)
                    ->where("tmp_appt_id",0)
                    ->where("is_event",0)
                    ->where("event_time",null)
                    ->where("hour_time",null)
                    ->where("user_id",$userid)
                    ->select('id')
                    ->first();
                                 
        if(!is_null($reminder))
        {
          $check = ReminderCustomers::where('reminder_id',$reminder->id)->whereIn("status",[2,5])->get();

          if($check->count() > 0)
          {
            try{
              ReminderCustomers::where('reminder_id',$reminder->id)->whereIn("status",[2,5])->update(['status'=>0]);
              $msg['success'] = 1;
            }
            catch(QueryException $e)
            {
              // $e->getMessage()
              $msg['success'] = 0;
            }
            return response()->json($msg);
          }
        }
    }

    /*public function exportListCSVSubscriber($list_id,$import){
        $id_user = Auth::id();
        $check = UserList::where('id',$list_id)->first();
        $day = Carbon::now()->toDateString();

        if($import == 1)
        {
          $filename = 'list-'.$check->label.'-'.$day.'-for-import.csv';
        }
        else
        {
          $filename = 'list-'.$check->label.'-'.$day.'-for-data.csv';
        }

        if(is_null($check))
        {
            return redirect('lists');
        }

        return Excel::download(new ListSubscribersExport($list_id,$import), $filename);
    }*/

    /* *************************************** 
        OLD CODES
     *************************************** */
   

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

    //display field after update
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

    //display field after update
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

/* end list controller */
}
