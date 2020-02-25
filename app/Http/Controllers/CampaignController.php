<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserList;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\BroadCastController;

class CampaignController extends Controller
{
    public function index()
    {
      $userid = Auth::id();
      $lists = UserList::where('user_id',$userid)->get();

      $data['lists'] = $lists;
      return view('campaign.campaign',$data);
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
          $event = new EventController;
          $saveEvent = $event->saveEvent($request);

          if(!empty($saveEvent))
          {
              $data['message'] = $saveEvent;
              return response()->json($data);
          }
      } 
      elseif($campaign == 'auto')
      {
          $auto = new ReminderController;
          $saveAutoReponder = $auto->saveAutoReponder($request);
          
          if(!empty($saveAutoReponder))
          {
              $data['message'] = $saveAutoReponder;
              return response()->json($data);
          }
      }
      else
      {
          $broadcast = new BroadCastController;
          $saveBroadcast = $broadcast->saveBroadCast($request);

          if(!empty($saveBroadcast))
          {
              $data['message'] = $saveBroadcast;
              return response()->json($data);
          }
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
