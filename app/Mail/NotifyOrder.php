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
            $message = null;
            $message .= 'Hai,'."\n";
            $message .= 'Kami mau mengingatkan, kamu belum melakukan transfer atas pembelian *Activrespon* dengan rincian: '."\n"."\n";
            $message .= 'No Order      : '.$orders["no"].''."\n";
            $message .= 'Package       : '.$orders["package"].''."\n";
            $message .= 'Harga         : '.number_format(Alert::pricing($orders["pack"])).''."\n";
            $message .= 'Discount      : '.$orders["discount"].''."\n";
            $message .= 'Total Tagihan : '.number_format($orders["total"]).''."\n"."\n";
            $message .= 'Silakan transfer sekarang ke'."\n";
            $message .= '*BCA :  8290-812-845 (Sugiarto Lasjim)*'."\n";
            $message .= 'Setelah transfer, jangan lupa konfirmasi ke link di bawah ini ya.'."\n";
            $message .= 'Klik ► '.url("order").''."\n"."\n";
            $message .= 'Salam sukses selalu,'."\n";
            $message .= '*Team Activrespon*'."\n";
            $message .= ' ------------------------------------------';

            //dd($message);
             
            // ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');
						ApiHelper::send_simi($phone,$message,env('REMINDER_PHONE_KEY'));
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
            $orders = $this->orders;
            $message = null;
            $message .= 'Halo,'."\n";
            $message .= 'Hari ini kamu akan kehilangan harga promo lhoo'."\n".'Yakin bisa rela?'."\n"."\n";
            $message .= 'Kamu akan kehilangan harga spesial yang sudah kamu'."\n".'dapatkan 5 hari yang lalu ketika order *Activrespon*.'."\n"."\n";
            $message .= 'Ini detail pembelianmu:'."\n";
            $message .= 'No Order      : '.$orders["no"].''."\n";
            $message .= 'Package       : '.$orders["package"].''."\n";
            $message .= 'Harga         : '.number_format(Alert::pricing($orders["pack"])).''."\n";
            $message .= 'Discount      : '.$orders["discount"].''."\n";
            $message .= 'Total Tagihan : '.number_format($orders["total"]).''."\n"."\n";
            $message .= 'Silakan transfer sekarang ke'."\n";
            $message .= '_BCA :  8290-812-845 (Sugiarto Lasjim)_'."\n"."\n";
            $message .= 'Mohon segera transfer dan konfirmasi sekarang karena hari ini'."\n".'karena pembelianmu akan dihapus oleh sistem.'."\n"."\n";
            $message .= 'Setelah transfer, jangan lupa konfirmasi pada link di bawah ini'."\n";
            $message .= 'Klik ► '.url('order').''."\n"."\n";
            $message .= 'Salam sukses selalu,'."\n";
            $message .= '*Team Activrespon*'."\n";
            $message .= ' ------------------------------------------';

            // ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');
						ApiHelper::send_simi($phone,$message,env('REMINDER_PHONE_KEY'));
            $data_email = [
              'no'=>$orders["no"],
              'package'=>$orders["package"],
              'price'=>Alert::pricing($orders["pack"]),
              'disc'=>$orders["discount"],
              'total'=>$orders["total"],
            ];

            return $this
              ->from('no-reply@activrespon.com', 'Activrespon')
              ->subject('Your order confirmation day 5')
              ->view('emails.notify.notif-order-2')
              ->with('data',$data_email);
             ;
        }
        
    }
}
