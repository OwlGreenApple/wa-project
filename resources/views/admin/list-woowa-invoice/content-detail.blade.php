@foreach($orders as $order)
  <tr>
    <td data-label="No Order">
      {{$order->no_order}}
    </td>
    <td data-label="Date">
      {{$order->created_at}}
    </td>
    <td data-label="grand_total">
      Rp. <?php echo number_format($order->grand_total); ?>
    </td>
    <td data-label="month">
      <?php echo $order->month; ?>
    </td>
    <td data-label="tagihan">
      Rp. <?php echo number_format($order->grand_total / $order->month); ?>
    </td>
  </tr>
@endforeach
<input type="hidden" name="total_tagihan" id="total_tagihan" value="{{$tagihan}}">