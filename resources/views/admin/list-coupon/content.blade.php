@foreach($coupons as $coupon)
  <tr>
    <td data-label="Kode Kupon">
      {{$coupon->kodekupon}}
    </td>
    <td data-label="Diskon (Nominal)">
      Rp. <?php echo number_format($coupon->diskon_value) ?>
    </td>
    <td data-label="Diskon (Persen)">
      {{$coupon->diskon_percent}}%
    </td> 
    <td data-label="Valid Until">
      {{$coupon->valid_until}}
    </td>
    <td data-label="Valid To">
      {{$coupon->valid_to}}
    </td>
    <td data-label="Keterangan">
      {{$coupon->keterangan}}
    </td>
    <td data-label="Paket">
      <?php 
          switch ($coupon->package_id) {
              case 0:
                echo 'All';
              break;
              case 1:
                echo 'Pro Monthly';
              break;
              case 2:
                echo 'Pro Yearly';
              break;
              case 3:
                echo 'Elite Monthly';
              break;
              case 4:
                echo 'Elite Yearly';
              break;
          }
       ?>
    </td>
    <td data-label="Action">
      <button type="button" class="btn btn-sm btn-primary btn-edit" data-toggle="modal" data-target="#edit-coupon" data-id="{{$coupon->id}}" data-kodekupon="{{$coupon->kodekupon}}" data-nominal="{{$coupon->diskon_value}}" data-persen="{{$coupon->diskon_percent}}" data-validuntil="{{$coupon->valid_until}}" data-validto="{{$coupon->valid_to}}" data-keterangan="{{$coupon->keterangan}}" data-paket="{{$coupon->package_id}}">
        <i class="fas fa-pen"></i>
      </button>  
      <button type="button" class="btn btn-sm btn-danger btn-delete" data-toggle="modal" data-target="#confirm-delete" data-id="{{$coupon->id}}">
        <i class="far fa-trash-alt"></i>
      </button>  
    </td>
  </tr>
@endforeach