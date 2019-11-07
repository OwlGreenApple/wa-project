<?php

namespace App\Exports;

use App\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\WithMapping;

class ListSubscribersExport implements FromCollection, WithMapping, Responsable
{
  
   use Exportable;

   public function __construct(int $listid)
    {
        $this->idlist = $listid;
    }

    private $writerType = Excel::CSV;

	 private $headers = [
        'Content-Type' => 'text/csv',
    ];

    /*public function query()
    {
    	$id_user = Auth::id();
        return Customer::query()->where([['list_id',$this->idlist],['user_id','=',$id_user]])->select('name','wa_number','additional');
    }*/

     public function collection()
    {
    	$id_user = Auth::id();
        return Customer::query()->where([['list_id',$this->idlist],['user_id','=',$id_user]])->select('name','wa_number','additional')->get();
    }

    public function map($customer): array
    {
    	//$parse = json_decode($customer->additional,true);
        return [
            $customer->name,
            $customer->wa_number,
            $customer->additional
        ];
    }

}
