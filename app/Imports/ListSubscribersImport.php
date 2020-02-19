<?php

namespace App\Imports;

use App\Customer;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Facades\Auth;

class ListSubscribersImport implements ToModel
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

    public function model(array $row)
    {
        /*if($row[2] <> null)
        {
            $converting = [];
            $replace = preg_replace("/\n/i", "", $row[2]);
            $removespace = preg_replace('/\s+/', '', $replace);
            $replace = explode(",",$removespace);

            foreach($replace as $rows)
            {
                $converting[] = explode("=",$rows);
            }
        }
        $result = array(
           'user_id'  => Auth::id(),
           'list_id'  => $this->id_list,
            $customer->name,
            $customer->wa_number
        );
        */
        //dd(count($row));
        //die('');
        ++$this->rows;
        return new Customer([
           'user_id'  => Auth::id(),
           'list_id'  => $this->id_list,
           'name'     => $row[0],
           'telegram_number'=> $row[1],
           'email'=> $row[2],
           'username' => $row[3]
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
