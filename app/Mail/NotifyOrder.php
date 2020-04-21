<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\ApiHelper;

class NotifyOrder extends Mailable
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

        if($this->day == 1)
        {
            $phone = $this->phone;
            $message = 'Test mesage _1 days_';
            ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

            return $this
              ->from('no-reply@activrespon.com', 'Activrespon')
              ->subject('Your order confirmation day 1')
              ->view('emails.notify.notif-order')
              ->with($this->day);
             ;
        }
        elseif($this->day == 5)
        {
            $phone = $this->phone;
            $message = 'Test mesage _5 days_';
            ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

            return $this
              ->from('no-reply@activrespon.com', 'Activrespon')
              ->subject('Your order confirmation day 5')
              ->view('emails.notify.notif-order-2')
              ->with($this->day);
             ;
        }
        
    }
}
