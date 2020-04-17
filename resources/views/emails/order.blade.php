Terima kasih, anda telah melakukan pemesanan Activrespon service.<br>
Info Order anda adalah sebagai berikut <br>
<br>
<strong>No Order :</strong> {{$no_order}} <br>
<strong>Nama :</strong> {{$user->name}} <br>
<strong>Status Order :</strong> Pending <br>
Anda telah memesan Paket {{$nama_paket}} <br>
<br>
<strong>Rp. {{number_format($order->total)}} </strong><br>
<?php if ($order->total_upgrade>0) { ?>
	<strong>Upgrade Price :</strong>Rp. {{number_format($order->total_upgrade)}} </strong><br>
<?php } ?>
<strong>Diskon :</strong>Rp. {{number_format($order->discount)}} </strong><br>
<strong>Total :</strong>Rp. {{number_format($order->grand_total)}} </strong><br>

<br>
	Harap SEGERA melakukan pembayaran,<br> 
	<strong>TRANSFER Melalui :</strong><br>
	<br>
	<strong>Bank BCA</strong><br>
    8290-812-845<br>
    Sugiarto Lasjim<br>
  <br>
	
	
	Dan setelah selesai membayar<br>
	Silahkan lakukan konfirmasi pembayaran di menu Orders, atau bisa dengan mengklik <a href="{{url('orders')}}"> --> KONFIRMASI PEMBAYARAN <--  </a> disini. <br>

<br> Salam hangat, 
<br>
Activrespon