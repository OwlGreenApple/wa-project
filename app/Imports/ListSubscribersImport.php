<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Rules\TelegramNumber;

use App\Http\Controllers\ListController;

class ListSubscribersImport implements ToCollection,WithStartRow 
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    use Importable;
    private $rows = 0;

    public function __construct(int $idlist)
    {
        $this->id_list = $idlist;
    }
    
    public function collection(Collection $rows)
    { 
        $data = $rows->toArray();
        $cell = $rules_phone = $rules_username = array();

        if(count($data) > 0)
        {
            foreach($data as $index => $col)
            {
                $cell[] = array(
                    'name'=>$col[0],
                    'phone'=>$col[1],
                    'email'=>$col[2],
                    'username'=>$col[3],
                );
            }
        }

        $niceNames = array(
            '*.name' => 'name',
            '*.phone' => 'phone',
            '*.email' => 'email',
            '*.username' => 'telegram username',
        );

        $messages = [
            'required_if'=>'The :attribute field is required when :other is blank'
        ];

        $rules = [
           '*.name'=> ['required'],
           '*.phone'=> ['required_if:*.username,==,'.null.'',new TelegramNumber],
           '*.email'=> ['required','email'],
           '*.username'=> ['required_if:*.phone,==,'.null.''],
        ];

        $validator = Validator::make($cell,$rules,$messages); 
        $validator->setAttributeNames($niceNames);
        $validator->validate(); 

        if(count($rows) > 0)
        {
            foreach ($rows as $row) {
                if($row[1] == '' && $row[3] == '')
                {
                    continue;
                }

                if(empty($row[3]))
                {
                    $row[1] = $this->checkPhone($row[1]);
                    $checkunique = $this->checkUniquePhone($row[1],$row[2]);
                }
                else {
                    $row[1] = 0;
                    $checkunique = $this->checkUniqueUsername($row[3],$row[2]);
                }

                
                $list = new ListController;

                if($row[1] <> null){
                  $chat_id = $list->getPhoneTelegramChatID($this->id_list,$row[1]);
                } else {
                  $chat_id = $list->getChatIDByUsername($phone,$row[3]);
                }
                

                if($checkunique == true)
                {
                  ++$this->rows;
                  Customer::create([
                     'user_id'  => Auth::id(),
                     'list_id'  => $this->id_list,
                     'name'     => $row[0],
                     'telegram_number'=> $row[1],
                     'email'=> $row[2],
                     'username' => $row[3],
                     'chat_id' => $chat_id,
                  ]);
                }
            }
        }
    } 
    
    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function startRow(): int
    {
        return 4;
    }

    public function checkPhone($row){
        if(preg_match('/^0[0-9]*$/i',$row))
        {
            $row = ltrim($row,0);
        } 

        if(preg_match('/^[+]0[0-9]*$/i',$row))
        {
            $row = ltrim($row,'+0');
        }

        if(empty($row)) 
        {
            $row = 0;
        }

        if(!preg_match('/^[+][0-9]*$/i',$row) && $row <> 0)
        {
            $row = '+'.$row;
        } 

        return $row;
    }

    public function checkUniquePhone($number,$email){
        $telegramnumber = Customer::where([['telegram_number','=',$number],['email','=',$email]])->first();
        if(is_null($telegramnumber))
        {
           return true;
        }
        else {
           return false;
        }
    }
    
    public function checkUniqueUsername($username,$email){
        $telegram_username = Customer::where([['username','=',$username],['email','=',$email]])->first();
        if(is_null($telegram_username))
        {
           return true;
        }
        else {
           return false;
        }
    }

/* end class */    
}
