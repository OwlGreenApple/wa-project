 <table id="broadcast_list" class="display w-100">
  <thead class="bg-dashboard">
    <tr>
      <th class="text-center">No</th>
      <!--<th class="text-center">Day Send</th>-->
      <th class="text-center">Time Send</th>
      <th class="text-center">Name Contact</th>
      <th class="text-center">WA Contact</th>
      @if($active == 1)
        <th class="text-center">Delete</th>
      @else
        <th class="text-center">Status</th>
      @endif
    </tr>
  </thead>

  @if($campaigns->count() > 0)
    @php $x = 1 @endphp
    <tbody>
      @foreach($campaigns as $rows)
        <tr>
          <td class="text-center">{{ $x }}</td>
          <!--<td class="text-center">{{ $rows->day_send }}</td>-->
          <td class="text-center">{{ $rows->updated_at }}</td>
          <td class="text-center">{{ $rows->name }}</td>
          <td class="text-center">{{ $rows->telegram_number }}</td>
          @if($active == 1)
            <td class="text-center"><a id="{{ $rows->bcsid }}" data-broadcast="1" class="icon-cancel"></a></td>
          @else
            <td class="text-center">
              @if($rows->status == 1)
                Success
              @elseif($rows->status == 2)
                <span class="act-tel-apt-create">Phone Offline</span> 
              @elseif($rows->status == 3)
                <span class="act-tel-apt-create">Phone Not Available</span>
              @else
                <span class="act-tel-apt-create">Cancelled</span>
              @endif
            </td>
          @endif
        </tr>
       @php $x++ @endphp
      @endforeach
    </tbody>
  @endif
</table>

<script type="text/javascript">
  $(document).ready(function(){
    table_broadcast();
  });

  function table_broadcast()
  {
    $("#broadcast_list").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
    });
  }
</script>