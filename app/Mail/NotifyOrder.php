<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\ApiHelper;
use App\Helpers\Alert;

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
    public $orders;
    public function __construct($day,$phone,$orders)
    {
        $this->day = $day;
        $this->phone = $phone;
        $this->orders = $orders;
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
            $orders = $this->orders;
            $message = '
              Hai,
              Kami mau mengingatkan, kamu belum melakukan transfer atas pembelian *Activrespon* dengan rincian: 

              No Order        : '.$orders["no"].'
              Package         : '.$orders["package"].'
              Harga           : '.Alert::pricing($orders["pack"]).'
              Discount        : '.$orders["discount"].'
              Total Tagihan   : '.$orders["total"].'

              Silakan transfer sekarang ke
              *BCA :  8290-812-845 (Sugiarto Lasjim)*

              Setelah transfer, jangan lupa konfirmasi ke link di bawah ini ya. 
              Klik ►

              Salam sukses selalu,
              *Team Activrespon*
              ------------------------------------------
              ';
            ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');
            $data_email = [
              'no'=>$orders["no"],
              'package'=>$orders["package"],
              'price'=>Alert::pricing($orders["pack"]),
              'disc'=>$orders["discount"],
              'total'=>$orders["total"],
            ];

            return $this
              ->from('no-reply@activrespon.com', 'Activrespon')
              ->subject('Your order confirmation day 1')
              ->view('emails.notify.notif-order')
              ->with('data',$data_email);
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
