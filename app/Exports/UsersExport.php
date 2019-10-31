<?php

namespace App\Exports;

//use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Excel;
use Illuminate\Contracts\Support\Responsable;
use App\Customer;

class UsersExport implements FromQuery, Responsable 
{

	use Exportable;

	public function __construct(int $idreminder)
    {
        $this->id_reminder = $idreminder;
    }


	//private $fileName = 'users.csv';

	private $writerType = Excel::CSV;

	 private $headers = [
        'Content-Type' => 'text/csv',
    ];

     public function query()
    {
    	$id_user = Auth::id();
        return Customer::query()->where([['list_id',$this->id_reminder],['user_id','=',$id_user]])->select('name','wa_number');
    }

/* end UsersExport */
}
