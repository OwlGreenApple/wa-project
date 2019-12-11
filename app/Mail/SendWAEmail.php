<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWAEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $emaildata;
    public $subject;

    public function __construct($emaildata,$subject)
    {
        $this->emaildata = $emaildata;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
          return $this
        ->from('no-reply@activwa.com', 'ActivWA')
        ->subject($this->subject)
        ->view('emails.test-email')
        ->with($this->emaildata);
    }
}
