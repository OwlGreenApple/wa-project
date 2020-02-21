<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;

class CampaignController extends Controller
{
    public function index()
    {
      return view('campaign.campaign');
    }

    public function CreateCampaign() 
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data = array(
          'lists'=>$lists,
      );

      return view('campaign.create-campaign',$data);
    }

    public function SaveCampaign(Request $request)
    {
      $campaign = $request->campaign;

      if($campaign == 'event')
      {

      } 
      elseif($campaign == 'auto')
      {

      }
      else
      {

      }
      
    }

    public function addReminder()
    {
      return view('campaign.add-reminder');
    } 

    public function reportReminder()
    {
      return view('campaign.report-reminder');
    }


/* end controller */
}
