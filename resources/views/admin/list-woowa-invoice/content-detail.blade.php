
@foreach($orders as $order)
  <tr>
    <td data-label="No Order">
      {{$order->no_order}}
    </td>
    <td data-label="grand_total" align="right">
      Rp. <?php echo number_format($order->grand_total); ?>
    </td>
    <td data-label="month" align="center">
      <?php echo $order->month; ?>
    </td>
    <td data-label="tagihan" align="right">
      Rp. <?php echo number_format($order->grand_total / $order->month); ?>
    </td>
    <td data-label="Date" align="right">
      {{$order->created_at}}
    </td>
  </tr>
@endforeach