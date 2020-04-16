<table id="list_appointment" class="display" style="width : 100%">
  <thead class="bg-dashboard">
    <tr>
      <th class="text-center">No</th>
      <th class="text-center">Date Appointment</th>
      <th class="text-center">H-</th>
      <th class="text-center">Name Contact</th>
      <th class="text-center">WA Contact</th>
      @if($active == 1)
        <th class="text-center">Edit</th>
        <th class="text-center">Delete</th>
      @else
        <th class="text-center">Status</th>
      @endif
    </tr>
  </thead>

  <tbody>
     @if($active == 1)
        @if($campaigns->count() > 0)
          @php $x =1 @endphp
          @foreach($campaigns as $row)
            <tr>
              <td class="text-center">{{ $x }}</td>
              <td class="text-center">{{ $row->event_time }}</td>
              <td class="text-center">H-{{ abs($row->days) }}</td>
              <td class="text-center">{{ $row->name }}</td>
              <td class="text-center">{{ $row->telegram_number }}</td>
              <td class="text-center">
                <a id="{{ $row->campaign_id }}" data-ev="{{ $row->event_time }}" data-name="{{ $row->name }}" data-phone="{{ $row->telegram_number }}" data-customer-id="{{ $row->id }}" class="icon-edit"></a>
              </td>
              <td class="text-center"><a id="{{ $row->rid }}" class="icon-cancel"></a></td> 
            </tr> 
            @php $x++ @endphp
          @endforeach
        @endif
    @else <!-- inactive -->
        @if($campaigns->count() > 0)
          @php $x =1 @endphp
          @foreach($campaigns as $row)
            <tr>
              <td class="text-center">{{ $x }}</td>
              <td class="text-center">{{ $row->event_time }}</td>
              <td class="text-center">H-{{ abs($row->days) }}</td>
              <td class="text-center">{{ $row->name }}</td>
              <td class="text-center">{{ $row->telegram_number }}</td>
              <td colspan="2" class="text-center">{!! $alert->message_status($row->status) !!}</td>
            </tr> 
            @php $x++ @endphp
          @endforeach
        @endif
    @endif
  </tbody>
</table>

<script type="text/javascript">
  $(document).ready(function(){
    tableData();
  });
   
  function tableData()
  {
    $("#list_appointment").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
    });
  }
</script>