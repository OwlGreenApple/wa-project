<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;
use App\Customer;

class UsersImport implements ToModel
{

	use Importable;

	public function __construct(int $idlist)
    {
        $this->id_list = $idlist;
    }

    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {

    	if(empty($row[1]))
    	{
    		$row[1] = '';
    	}

    	if(empty($row[8]))
    	{
    		return null;
    	}

    	if(preg_match('/^[62][0-9]*$/',$row[8])){
    		$row[8] = '+'.$row[8];
    	}

    	if(preg_match('/^[0][0-9]*$/',$row[8])){
    		$row[8] = str_replace('0','+62',$row[8]);
    	}

    	if(preg_match('/^[1-9][0-9]*$/',$row[8])){
    		$row[8] = '+62'.$row[8];
    	}

        return new Customer([
           'user_id'  => Auth::id(),
           'list_id'  => $this->id_list,
           'name'     => $row[1],
           'wa_number'=> $row[8],
        ]);
    }
}
