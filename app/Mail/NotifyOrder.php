<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $day;
    public function __construct($day)
    {
        $this->day = $day;
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
            return $this
              ->from('no-reply@activrespon.com', 'Activrespon')
              ->subject('Your order confirmation day 1')
              ->view('emails.notify.notif-order')
              ->with($this->day);
             ;
        }
        elseif($this->day == 5)
        {
            return $this
              ->from('no-reply@activrespon.com', 'Activrespon')
              ->subject('Your order confirmation day 5')
              ->view('emails.notify.notif-order-2')
              ->with($this->day);
             ;
        }
        
    }
}
