<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\ApiHelper;

class MemberShip extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $day;
    public $phone;

    public function __construct($day,$phone)
    {
        $this->day = $day;
        $this->phone = $phone;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->day == 5)
        {
            $phone = $this->phone;
            $message = 'Test mesage *5 days*';
            ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

            return $this
            ->from('no-reply@activrespon.com', 'Activrespon')
            ->subject('Expired membership day -5')
            ->view('emails.membership.exp-membership-5')
            ->with($this->day);
           ;
        }
        elseif($this->day == 1)
        {
           $phone = $this->phone;
           $message = 'Test mesage *1 days*';
           ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

           return $this
            ->from('no-reply@activrespon.com', 'Activrespon')
            ->subject('Expired membership day -1')
            ->view('emails.membership.exp-membership-1')
            ->with($this->day);
           ;
        } 
        elseif($this->day == -1)
        {
           $phone = $this->phone;
           $message = 'Test mesage *1 days after expired*';
           ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

           return $this
            ->from('no-reply@activrespon.com', 'Activrespon')
            ->subject('Expired membership day +1')
            ->view('emails.membership.exp-membership_plus_1')
            ->with($this->day);
           ;
        }
    }
}
