<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\UserList;
use App\Customer;
use App\User;

class AppointmentController extends Controller
{
    function index()
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data['lists'] = $lists;
      return view('appointment.index',$data);
    }

    function createAppointment()
    {
        return view('appointment.create_apt');
    }

    function listAppointment()
    {
        return view('appointment.list_apt');
    }

    function formAppointment()
    {
        return view('appointment.form_apt');
    }
}
