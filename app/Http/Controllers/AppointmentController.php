<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;

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

    }

    function formAppointment()
    {

    }
}
