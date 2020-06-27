<div>Halo,</div>
<div>Hari ini kamu akan kehilangan harga promo lhoo</div>
<div>Yakin bisa rela?</div>
<br/>

<div>Kamu akan kehilangan harga spesial yang sudah kamu</div>
<div>dapatkan 5 hari yang lalu ketika order <b>Activrespon</b>.</div>
<br/>

<div>Ini detail pembelianmu:</div>
<div>No Order        : {{ $data["no"] }}</div>
<div>Package         : {{ $data["package"] }}</div>
<?php if ($data["disc"]>0 || $data["price"]<$data["total"]) { ?>
<div>Harga           : {{ str_replace(",",".",number_format($data["price"])) }}</div>
<?php } ?>
<?php if ($data["disc"]>0) { ?>
<div>Discount        : {{ str_replace(",",".",number_format($data["disc"])) }}</div>
<?php } ?>
<div>Total Tagihan   : {{ str_replace(",",".",number_format($data["total"])) }}</div>

<br/>
Silakan transfer sekarang ke<br/>
<i>BCA :  8290-336-261 (Sugiarto Lasjim)</i><br/><br/>

<div>Mohon segera transfer & konfirmasi sekarang karena hari ini</div>
<div>karena pembelianmu akan dihapus oleh sistem.</div>
<br/>

<div>
Setelah transfer, jangan lupa konfirmasi ke link di bawah ini ya. <br/>
Klik ► <a href="{{ url('order') }}" target="_blank">Konfirmasi Pembayaran</a>
</div><br/>

Salam sukses selalu,<br/>
<b>Team Activrespon</b><br/>
------------------------------------------