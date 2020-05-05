<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\UserList;
use App\BroadCast;
use App\BroadCastCustomers;
use App\Reminder;
use App\ReminderCustomers;
use App\Customer;
use App\Helpers\Spintax;
use Carbon\Carbon;
use App\User;
use App\PhoneNumber;
use DB;
use App\Helpers\ApiHelper;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send message message to customer according on broadcast or event or auto responder or appointment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
      //Broadcast 
      $this->campaignBroadcast();
   
      //Auto Responder
      $this->campaignAutoResponder();
      
      //Event
      $this->campaignEvent();
      
      //Appointment
      $this->campaignAppointment();
    }    
 
    /* BROADCAST */
    public function campaignBroadcast()
    {
        $broadcast = BroadCast::select("broad_casts.*","broad_cast_customers.*","broad_cast_customers.id AS bccsid","phone_numbers.id AS phoneid","users.id","customers.*","users.timezone","users.email")
          ->join('users','broad_casts.user_id','=','users.id')
          ->join('broad_cast_customers','broad_cast_customers.broadcast_id','=','broad_casts.id')
          ->join('phone_numbers','phone_numbers.user_id','=','broad_casts.user_id')
          ->join('customers',"customers.id","=","broad_cast_customers.customer_id")
          ->where("broad_cast_customers.status",0)
          ->where("customers.status",1)
          ->orderBy('broad_casts.user_id')
          ->get();

        if($broadcast->count() > 0)
        {
            $number = 0;
            foreach($broadcast as $row)
            {
                // $customers = Customer::where('id',$row->customer_id)->first();
                $customer_message = $row->message;
                $customer_phone = $row->telegram_number;
                $phoneNumber = PhoneNumber::find($row->phoneid);

                if(is_null($phoneNumber))
                {
                   continue;
                }

                $hour = $row->hour_time; //hour according user set it to sending
                $date = Carbon::parse($row->day_send);

                // if(!is_null($customers))
                // {
                    $message = $this->replaceMessage($customer_message,$row->name,$row->email,$customer_phone);
                    $chat_id = $row->chat_id;  
                    $counter = $phoneNumber->counter;
                    $max_counter = $phoneNumber->max_counter;
                    $max_counter_day = $phoneNumber->max_counter_day;
                    $key = $phoneNumber->filename;
                    $now = Carbon::parse(Carbon::now())->timezone($row->timezone);

                    $time_sending = $date->toDateString().' '.$hour;
                    $deliver_time = Carbon::parse($time_sending)->diffInSeconds($now, false);
                    // $deliver_time = Carbon::parse($time_sending)->diffInSeconds(Carbon::now(), false);
                    $midnightTime = $this->avoidMidnightTime($row->timezone);
                    
                    if($deliver_time < 0 || $midnightTime == false){
                      //klo blm hour_time di skip dulu
                      continue;
                    }

                    if(($counter <= 0) || ($max_counter <= 0) || ($max_counter_day <= 0) ) {
                        continue;
                    }

                    if($counter > 0)
                    {
                        $campaign = 'broadcast';
                        $id_campaign = $row->bccsid;

                        //status
												$broadcastCustomer = BroadCastCustomers::find($row->bccsid);
												if (is_null($broadcastCustomer)) {
													continue;
												}
												if ($broadcastCustomer->status==5) {
													continue;
												}
												$broadcastCustomer->status = 5;
												$broadcastCustomer->save();

                        $status = 'Sent';
                        $number ++;

												/*if ($row->email=="activomnicom@gmail.com") {
													$send_message = ApiHelper::send_message_android(env('BROADCAST_PHONE_KEY'),$message,$customer_phone,"reminder");
												}
												else {*/
													if ($row->image==""){
														// $send_message = ApiHelper::send_message($customer_phone,$message,$key);
														// $send_message = ApiHelper::send_wanotif($customer_phone,$message,$key);
														$send_message = ApiHelper::send_simi($customer_phone,$message,$key);
													}
													else {
														$send_message = ApiHelper::send_image_url($customer_phone,Storage::disk('s3')->url($row->image),$message,$key);
													}
												//}
												sleep(3);
                        // $this->generateLog($number,$campaign,$id_campaign,$status);
                        $this->generateLog($number,$campaign,$id_campaign,$send_message);
                        $status = $this->getStatus($send_message);
                        
                        $phoneNumber->counter --;

                        if($max_counter > 0)
                        {
                          $phoneNumber->max_counter --;
                        }
                        if($max_counter_day > 0)
                        {
                          $phoneNumber->max_counter_day --;
                        }
                        $phoneNumber->save();
                        
												$broadcastCustomer->status = $status;
												$broadcastCustomer->save();
                    }
                    else {
                        $campaign = 'broadcast';
                        $id_campaign = $row->bccsid;
                        $status = 'Error';
                        $number ++;
                        $this->generateLog($number,$campaign,$id_campaign,$status);
                    }
                // }
                sleep(7);
            }//END LOOPING

        } // END BROADCAST 
    }

    /* AUTO RESPONDER */
    public function campaignAutoResponder()
    {
        // Reminder 
        // $current_time = Carbon::now();
        $reminder = Reminder::where([
            ['reminder_customers.status','=',0],
            ['reminders.is_event','=',0],
            ['reminders.status','=',1],
            ['customers.status','=',1],
            // ['customers.created_at','<=',$current_time->toDateTimeString()],
            ])
            // ->whereRaw('DATEDIFF(now(),customers.created_at) >= reminders.days')
            ->join('users','reminders.user_id','=','users.id')
            ->rightJoin('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
            ->join('customers','customers.id','=','reminder_customers.customer_id')
            ->select('reminder_customers.id AS rcs_id','reminder_customers.status AS rc_st','reminders.*','customers.created_at AS cstreg','customers.telegram_number','customers.name','customers.email','reminders.id AS rid','reminders.user_id AS userid','users.timezone','users.email as useremail')
            ->get();

        $number = $counter = $max_counter = 0;

        if($reminder->count() > 0)
        {
            foreach($reminder as $col) 
            {
                $phoneNumber = PhoneNumber::where('user_id','=',$col->userid)->first();
            
                if(!is_null($phoneNumber)){
                  $counter = $phoneNumber->counter;
                  $max_counter = $phoneNumber->max_counter;
                  $max_counter_day = $phoneNumber->max_counter_day;
                }
                else
                {
                  continue;
                }

                $key = $phoneNumber->filename;
                $customer_phone = $col->telegram_number;
                $customer_message = $col->message;
                $customer_name = $col->name;
                $customer_mail = $col->email;

                $hour_time = $col->hour_time;
                $day_reminder = $col->days; // how many days
                $customer_signup = Carbon::parse($col->cstreg)->addDays($day_reminder);
                $adding_with_hour = $customer_signup->toDateString().' '.$hour_time;

                $reminder_customer_status = $col->rc_st;
                $reminder_customers_id = $col->rcs_id;
								
								//status queued 
								$remindercustomer_update = ReminderCustomers::find($reminder_customers_id);
								if ($remindercustomer_update->status==5) {
									continue;
								}
								$remindercustomer_update->status = 5;
								$remindercustomer_update->save();
								
                $now = Carbon::now()->timezone($col->timezone);
                $adding = Carbon::parse($adding_with_hour);         
                $number++;
                $midnightTime = $this->avoidMidnightTime($row->timezone);

                if(($counter <= 0) || ($max_counter <= 0) || ($max_counter_day <= 0) || $midnightTime == false) {
                  continue;
                }

                // if($adding->lte(Carbon::now()) && $counter > 0)
                if($adding->lte($now) && $counter > 0)
                {        
                    $message = $this->replaceMessage($customer_message,$customer_name,$customer_mail,$customer_phone);

										/*if ($col->useremail=="activomnicom@gmail.com") {
											$send_message = ApiHelper::send_message_android(env('BROADCAST_PHONE_KEY'),$message,$customer_phone,"reminder");
										}
										else {*/
											if ($col->image==""){
												// $send_message = ApiHelper::send_message($customer_phone,$message,$key);
												// $send_message = ApiHelper::send_wanotif($customer_phone,$message,$key);
												$send_message = ApiHelper::send_simi($customer_phone,$message,$key);
											}
											else {
												$send_message = ApiHelper::send_image_url($customer_phone,Storage::disk('s3')->url($col->image),$message,$key);
											}
										//}

										sleep(3);
                    $campaign = 'Auto Responder';
                    $id_campaign = 'reminder_customers_id = '.$col->rcs_id;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);

                    $status = $this->getStatus($send_message);
                    $remindercustomer_update = ReminderCustomers::find($reminder_customers_id);
                    $remindercustomer_update->status = $status;
                    $remindercustomer_update->save();

                    $phoneNumber->counter--;

                    if($max_counter > 0)
                    {
                        $phoneNumber->max_counter--;
                    }
                    if($max_counter_day > 0)
                    {
                        $phoneNumber->max_counter_day--;
                    }

                    $phoneNumber->save();
                }
                else 
                {
                    $campaign = 'Auto Responder';
                    $id_campaign = 'reminder_customers_id = '.$col->rcs_id;
                    $status = 'Not Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);
                    continue;
                }
                sleep(7);
            }//END LOOPING
        }
    }

    /* EVENT */
    public function campaignEvent()
    {
          $idr = null;
          $event = null;
          $today = Carbon::now();

          $reminder = Reminder::where([
                  ['reminder_customers.status',0], 
                  ['reminders.is_event',1], 
                  ['customers.status',1], 
                  ['reminders.status','=',1],
          ])
          ->join('users','reminders.user_id','=','users.id')
          ->join('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.*','reminder_customers.id AS rcs_id','customers.name','customers.telegram_number','customers.email','users.timezone','users.email as useremail','users.membership')
          ->get();

          if($reminder->count() > 0)
          {
              $number = $counter = 0;
              foreach($reminder as $row)
              {
                $id_reminder = $row->id;
                $event_date = Carbon::parse($row->event_time);
                $days = (int)$row->days;
                $hour = $row->hour_time; //hour according user set it to sending
                $membership = $row->membership;
                $midnightTime = $this->avoidMidnightTime($row->timezone);

                $phoneNumber = PhoneNumber::where('user_id','=',$row->user_id)->first();

                if(!is_null($phoneNumber)){
                  $customer_phone = $row->telegram_number;
                  $key = $phoneNumber->filename;
                  $counter = $phoneNumber->counter;
                  $max_counter = $phoneNumber->max_counter;
                  $max_counter_day = $phoneNumber->max_counter_day;
                }
                else
                {
                  continue;
                }

                // PREVENT RUN IF MEMBERSHIP LESS THAN 2
                if(getMembership($membership) < 2 || !is_numeric(getMembership($membership)) || $midnightTime == false )
                {
                    continue;
                }

								if(($counter <= 0) || ($max_counter <= 0) || ($max_counter_day <= 0) ) {
									continue;
								}
                // if the day before / substract 
                if($days < 0){
                  $days = abs($days);
                  $date = $event_date->subDays($days);
                } else {
                  $date = $event_date->addDays($days);
                }

                $time_sending = $date->toDateString().' '.$hour;
                $now = Carbon::now()->timezone($row->timezone);
                $deliver_time = Carbon::parse($time_sending)->diffInSeconds($now, false);
                // $deliver_time = Carbon::parse($time_sending)->diffInSeconds(Carbon::now(), false);

                // get id reminder for reminder customer
                if($deliver_time >= 0 && $counter > 0){
                  $number++;
                  $campaign = 'Event';
                  $id_campaign = $row->rcs_id;

                  //status queued
									$remindercustomer_update = ReminderCustomers::find($id_campaign);
									if ($remindercustomer_update->status==5) {
										continue;
									}
                  $remindercustomer_update->status = 5;
                  $remindercustomer_update->save();

                  $status = 'Sent';
                  $id_reminder = $row->id_reminder;
                  
                  $message = $this->replaceMessage($row->message,$row->name,$row->email,$customer_phone);

									/*if ($row->useremail=="activomnicom@gmail.com") {
										$send_message = ApiHelper::send_message_android(env('BROADCAST_PHONE_KEY'),$message,$customer_phone,"reminder");
										if ($send_message) {
											$send_message="success";
										}
									}
									else {*/
										if ($row->image==""){
											// $send_message = ApiHelper::send_message($customer_phone,$message,$key);
											// $send_message = ApiHelper::send_wanotif($customer_phone,$message,$key);
											$send_message = ApiHelper::send_simi($customer_phone,$message,$key);
										}
										else {
											$send_message = ApiHelper::send_image_url($customer_phone,Storage::disk('s3')->url($row->image),$message,$key);
										}
									// }
									sleep(3);
                  $status = $this->getStatus($send_message);
                  $this->generateLog($number,$campaign,$id_campaign,$status);
                  $remindercustomer_update = ReminderCustomers::find($id_campaign);
                  $remindercustomer_update->status = $status;
                  $remindercustomer_update->save();

                  $phoneNumber->counter--;

                  if($max_counter > 0)
                  {
                      $phoneNumber->max_counter--;
                  }
                  if($max_counter_day > 0)
                  {
                      $phoneNumber->max_counter_day--;
                  }
                  $phoneNumber->save();
                }
                else
                {
                    $campaign = 'Event';
                    $id_campaign = $row->rcs_id;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);
                    continue;
                }
                sleep(7);
              }//END FOR LOOP EVENT
          }
    }
    

    /* Appointment */
    public function campaignAppointment()
    {
          $idr = null;
          $event = null;
          $today = Carbon::now();

          $reminder = Reminder::where([
                  ['reminder_customers.status',0], 
                  ['reminders.is_event',2], 
                  ['reminders.tmp_appt_id',">",0], 
                  ['customers.status',1], 
                  ['reminders.status','=',1],
          ])
          ->join('users','reminders.user_id','=','users.id')
          ->join('reminder_customers','reminder_customers.reminder_id','=','reminders.id')
          ->join('customers','customers.id','=','reminder_customers.customer_id')
          ->select('reminders.*','reminder_customers.id AS rcs_id','customers.name','customers.telegram_number','customers.email','users.timezone','users.email as useremail','users.membership')
          ->get();

          if($reminder->count() > 0)
          {
              $number = $counter = 0;
              foreach($reminder as $row)
              {
                $id_reminder = $row->id;
                $event_date = Carbon::parse($row->event_time);
                $days = (int)$row->days;
                $hour = $row->hour_time; //hour according user set it to sending

                $phoneNumber = PhoneNumber::where('user_id','=',$row->user_id)->first();
                $customer_phone = $row->telegram_number;
                $customer_message = $row->message;
                $key = $phoneNumber->filename;
                $membership = $row->membership;

                $date_appt = $event_date->toFormattedDateString();
                $time_appt = $event_date->toTimeString();
                $midnightTime = $this->avoidMidnightTime($row->timezone);

                if(!is_null($phoneNumber)){
                  $counter = $phoneNumber->counter;
                  $max_counter = $phoneNumber->max_counter;
                  $max_counter_day = $phoneNumber->max_counter_day;

                }
                else
                {
                  continue;
                }

                if(getMembership($membership) < 2 || !is_numeric(getMembership($membership)) ||$midnightTime == false)
                {
                    continue;
                }

								if(($counter <= 0) || ($max_counter <= 0) || ($max_counter_day <= 0) ) {
									continue;
								}
								
                // if the day before / substract 
                if($days < 0){
                  $days = abs($days);
                  $date = $event_date->subDays($days);
                } else {
                  $date = $event_date->addDays($days);
                }

                $time_sending = $date->toDateString().' '.$hour;
                $now = Carbon::now()->timezone($row->timezone);
                $deliver_time = Carbon::parse($time_sending)->diffInSeconds($now, false);
                // $deliver_time = Carbon::parse($time_sending)->diffInSeconds(Carbon::now(), false);

                // get id reminder for reminder customer
                if($deliver_time >= 0 && $counter > 0){
                  $number++;
                  $campaign = 'Appointment';
                  $id_campaign = $row->rcs_id;

                  //queued status
									$remindercustomer_update = ReminderCustomers::find($id_campaign);
									if ($remindercustomer_update->status==5) {
										continue;
									}
                  $remindercustomer_update->status = 5;
                  $remindercustomer_update->save();

                  $status = 'Sent';

                  $message = $this->replaceMessageAppointment($customer_message,$row->name,$row->email,$customer_phone,$date_appt,$time_appt);
                  $id_reminder = $row->id_reminder;
     
									/*if ($row->useremail=="activomnicom@gmail.com") {
										$send_message = ApiHelper::send_message_android(env('BROADCAST_PHONE_KEY'),$message,$customer_phone,"reminder");
										if ($send_message) {
											$send_message="success";
										}
									}
									else {*/
										if ($row->image==""){
											// $send_message = ApiHelper::send_message($customer_phone,$message,$key);
											// $send_message = ApiHelper::send_wanotif($customer_phone,$message,$key);
											$send_message = ApiHelper::send_simi($customer_phone,$message,$key);
										}
										else {
											$send_message = ApiHelper::send_image_url($customer_phone,Storage::disk('s3')->url($row->image),$message,$key);
										}
									// }

									sleep(3);
                  $status = $this->getStatus($send_message);
                  $this->generateLog($number,$campaign,$id_campaign,$status);
                  $remindercustomer_update = ReminderCustomers::find($id_campaign);
                  $remindercustomer_update->status = $status;
                  $remindercustomer_update->save();

                  $phoneNumber->counter--;
                  $phoneNumber->max_counter--;
                  $phoneNumber->max_counter_day--;
                  $phoneNumber->save();
                }
                else
                {
                    $campaign = 'Appointment';
                    $id_campaign = $row->rcs_id;
                    $status = 'Sent';
                    $this->generateLog($number,$campaign,$id_campaign,$status);
                    continue;
                }
                sleep(7);
              }//END FOR LOOP EVENT
          }
    }
    

    public function generateLog($number,$campaign,$id_campaign,$error = null)
    {
        $timegenerate = Carbon::now();
        $logexists = Storage::disk('local')->exists('log/log.txt');
        $format = "No : ".$number.", Date and time : ".$timegenerate.", Type : ".$campaign.", id : ".$id_campaign.", Status : ".$error."\n";

        if($logexists == true)
        {
            $log = Storage::get('log/log.txt');
            $string = $log."\n".$format;
            Storage::put('log/log.txt',$string);
        }
        else
        {
            $string = $format;
            Storage::put('log/log.txt',$string);
        }
       
    }

    public function replaceMessage($customer_message,$name,$email,$phone)
    {
     
      $replace_target = array(
        '[NAME]','[EMAIL]','[PHONE]'
      );

      $replace = array(
        $name,$email,$phone
      );
      $message = str_replace($replace_target,$replace,$customer_message);
      return $message;
    }

    public function replaceMessageAppointment($customer_message,$name,$email,$phone,$date_appt,$time_appt)
    {
        $replace_target = array(
          '[NAME]','[EMAIL]','[PHONE]','[DATE-APT]','[TIME-APT]'
        );

        $replace = array(
          $name,$email,$phone,$date_appt,$time_appt
        );

        $message = str_replace($replace_target,$replace,$customer_message);
        return $message;
    }

    public function getStatus($send_message)
    {
      if(strtolower($send_message) == 'Success')
      {
          $status = 1;
      }
      elseif($send_message == 'phone_offline')
      {
          $status = 2;
      } 
      else
      {
          $status = 3;
      }

      return $status;
    }

    public function avoidMidnightTime($timezone)
    {
        $time = Carbon::now()->timezone($timezone);
        $start = Carbon::createFromTime(23,0,0,$timezone);
        $end = Carbon::createFromTime(5,0,0,$timezone)->addDays(1);

        if($time->gte($start) && $time->lte($end))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

/* End command class */    
}