<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
        $cell = array();

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

        Validator::make($cell, [
           '*.name'=> ['required'],
           '*.phone'=> ['required_if:*.username,==,null'],
           '*.email'=> ['required','email'],
           '*.username'=> ['required_if:*.phone,==,null'],
        ])->validate(); 

        foreach ($rows as $row) {
            if($row[1] == '' && $row[3] == '')
            {
                continue;
            }

            $list = new ListController;

            if(!empty($row[1])){
              $chat_id = $list->getPhoneTelegramChatID($this->id_list,$row[1]);
            } else {
              $chat_id = $list->getChatIDByUsername($this->id_list,$row[3]);
            }

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

  
    public function model(array $row)
    {
        if(empty($row[3]))
        {
            $row[1] = $this->checkPhone($row[1]);
            $checkunique = $this->checkUniquePhone($row[1]);
        }
        else {
            $row[1] = 0;
            $checkunique = $this->checkUniqueUsername($row[3]);
        }

        ++$this->rows;
        if($checkunique == true){
            return new Customer([
               'user_id'  => Auth::id(),
               'list_id'  => $this->id_list,
               'name'     => $row[0],
               'telegram_number'=> $row[1],
               'email'=> $row[2],
               'username' => $row[3]
            ]);
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

    public function checkUniquePhone($row){
        $telegramnumber = Customer::where('telegram_number','=',$row)->first();
        if(is_null($telegramnumber))
        {
           return true;
        }
        else {
           return false;
        }
    }
    
    public function checkUniqueUsername($row){
        $telegram_username = Customer::where('username','=',$row)->first();
        if(is_null($telegram_username))
        {
           return true;
        }
        else {
           return false;
        }
    }

    public function rules(): array
    {
        return [
             // Can also use callback validation rules
             /*'0' => function($attribute, $value, $onFailure) {
                  if ($value !== 'import1') {
                       $onFailure('Name is not Patrick Brouwers');
                  }
              }
              */
            '0'=> ['required'],
            '1'=> ['required_without:3'],
            '2'=> ['required','email'],
            '3'=> ['required_without:1'],
        ];
    }


    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'telegram_number.required_without' => 'You must fill telegram number or telegram username'."\n",
            'telegram_username.required_without' => 'You must fill telegram username or telegram number'."\n",
        ];
    }
}
