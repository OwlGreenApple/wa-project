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
            $message = null;
            $message .= 'Halo,'."\n";
            $message .= 'Senang sekali kamu masih mempercayai *Activrespon*'."\n";
            $message .= 'untuk meningkatkan engagement pelanggan bisnis online mu'."\n"."\n";
            $message .= 'Sayangnya, 5 hari lagi _waktu berlangganan Activrespon_ akan habis.'."\n";
            $message .= 'Kami rasa kamu telah merasakan sendiri bagaimana mudah, simple dan'."\n";
            $message .= 'efisiennya *Activrespon*,'."\n";
            $message .= 'dalam membantumu megoptimalkan kesempatan lebih dekat dengan customer.'."\n"."\n";
            $message .= 'Jika kamu ingin tetap efisien dan produktif,'."\n";
            $message .= 'Silakan perpanjang akun *Activrespon* dengan cara: ke user dashboard dan CLICK “Extend”'."\n";
            $message .= 'Atau klik ► _*'.url("pricing").'*_'."\n"."\n";
            $message .= 'Kamu dapat meneruskan service *Activrespon*'."\n";
            $message .= 'Hanya semudah Click dan tambah waktu Anda SEKARANG.'."\n"."\n";
            $message .= 'Salam Hangat dan Sukses selalu,'."\n"."\n";
            $message .= '*Activrespon*'."\n";

            ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

            return $this
            ->from('no-reply@activrespon.com', 'Activrespon')
            ->subject('Expired membership day -5')
            ->view('emails.membership.exp-membership-5')
            //->with($this->day);
           ;
        }
        elseif($this->day == 1)
        {
           $phone = $this->phone;
           $message = null;
           $message .= 'Halo,'."\n";
           $message .= 'Hari ini masa berlaku *Activrespon* akan berakhir, kecuali...'."\n";
           $message .= 'Bila kamu tertarik untuk tetap ingin mempermudah kinerja CS dalam mengingatkan klien,'."\n";
           $message .= 'mem-broadcast dan membuat appointment bisnis onlinemu.'."\n"."\n";
           $message .= 'Membangun bisnis online membuatmu tidak bisa mengurusi semuanya.'."\n";
           $message .= 'Apalagi urusan pengingat event maupun bertemu dengan klien.'."\n"."\n";
           $message .= 'Jangan biarkan lupa menjadi tabiat buruk dan bisnismu menjadi terganggu.'."\n";
           $message .= 'Tetap gunakan Activrespon dan urusan broadcast, reminder,'."\n";
           $message .= 'appointment menjadi lebih mudah.'."\n"."\n";
           $message .= 'Sebagai tanda apresiasi karena kamu adalah customer prioritas kami'."\n";
           $message .= 'Ini adalah Kupon potongan harga spesial sebesar XXX persen'."\n";
           $message .= 'yang *BERLAKU HANYA* untuk X hari'."\n";
           $message .= '(terhitung sejak hari ini)'."\n"."\n";
           $message .= '_SEGERA manfaatkan Kupon ini SEKARANG DAN_'."\n";
           $message .= '_Perpanjang waktu berlangganan anda HARI INI juga._'."\n"."\n";
           $message .= 'Ingat yah, hari ini terakhir,'."\n";
           $message .= '*PS : Kupon special hanya untukmu, silakan login terlebih dahulu'."\n";
           $message .= '*PSS : ini kuponnya kalau lupa ► --- nanti diketik saat check out'."\n"."\n";
           $message .= 'Salam sukses selalu,'."\n"."\n";
           $message .= '*Activrespon*';

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
           $message = null;
           $message .= 'Hai,'."\n"."\n";
           $message .= 'Hari ini *KUPON* Potongan Harga Customer Prioritas akan berakhir.'."\n";
           $message .= 'Ini adalah kesempatan terakhir untuk menggunakan kuponmu.'."\n";
           $message .= 'Besok *Potongan Harga* ini tidak dapat digunakan lagi.'."\n"."\n";
           $message .= 'Sayang sekali apabila kamu melewatkan kesempatan ini, bukan?'."\n"."\n";
           $message .= 'SEGERA manfaatkan Kupon ini SEKARANG DAN'."\n";
           $message .= 'Perpanjang waktu berlangganan HARI INI juga.'."\n"."\n";
           $message .= 'Ingat! WAKTU sangat berharga.'."\n";
           $message .= 'Manfaatkan MOMENTUM yang sudah kamu bangun selama ini.'."\n";
           $message .= 'Jangan biarkan Kompetitor mendahuluimu.'."\n"."\n";
           $message .= 'Ingat yah, hari ini terakhir lho,'."\n";
           $message .= '*PS : Kupon special hanya untukmu, silakan login terlebih dahulu'."\n"."\n";
           $message .= 'Salam sukses selalu,'."\n"."\n";
           $message .= '*Activrespon*'."\n";

           ApiHelper::send_message_android(env('REMINDER_PHONE_KEY'),$message,$phone,'reminder');

           return $this
            ->from('no-reply@activrespon.com', 'Activrespon')
            ->subject('Expired membership day +1')
            ->view('emails.membership.exp-membership_plus_1')
            //->with($this->day);
           ;
        }
    }
}
