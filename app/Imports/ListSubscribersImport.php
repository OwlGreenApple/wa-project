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

   /* public function collection(Collection $rows)
    {
        $userid = Auth::id();
        foreach ($rows as $row) 
        {
            ++$this->rows;
            $addt = json_decode($row[2],true);
            Customer::create([
                'user_id'=>$userid,
                'list_id'=>$this->id_list,
                'name' => $row[0],
                'wa_number' => $row[1],
                'additional' => json_encode($addt),
            ]);
        }
    }*/

    public function model(array $row)
    {
        ++$this->rows;
        return new Customer([
           'user_id'  => Auth::id(),
           'list_id'  => $this->id_list,
           'name'     => $row[0],
           'wa_number'=> $row[1],
           'additional' => $row[2]
        ]);
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }
}
