<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreateDeviceController extends Controller
{
    public function index()
    {
    	return view('device.create-device');
    }

    public function devicePackage()
    {
    	return view('device.pricing');
    }

    public function checkout()
    {
    	return view('device.checkout');
    }

    public function thankYou()
    {
    	return view('device.thankyou');
    }

    public function temporary()
    {
    	return view('device.temporary');
    }
}
