<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\ReminderCustomers;

class UsersExport implements FromCollection
{

	public function __construct($query) {
	    $this->query = $query;
	  }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return ReminderCustomer::where();
    }
}
