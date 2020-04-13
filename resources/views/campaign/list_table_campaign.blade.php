@if($active == true)
    @if($campaigns->count() > 0)
      @php $x =1 @endphp
      @foreach($campaigns as $row)
        <tr>
          <td class="text-center">{{ $x }}</td>
          @if($is_event == 1)
            <td class="text-center">{{ $row->event_time }}</td>
            <td class="text-center">H{{ $row->days }}</td>
          @endif
          @if($is_event == 0)
            <td class="text-center">H+{{ abs($row->days) }}</td>
          @endif
          <td class="text-center">{{ $row->name }}</td>
          <td class="text-center">{{ $row->telegram_number }}</td>
          <td class="text-center">
            <a id="{{ $row->campaign_id }}" data-ev="{{ $row->event_time }}" data-name="{{ $row->name }}" data-phone="{{ $row->telegram_number }}" data-customer-id="{{ $row->id }}" class="icon-edit"></a>
          </td>
          <td class="text-center"><a id="{{ $row->rid }}" class="icon-cancel"></a></td> 
          <!-- <td class="text-center"><a id="{{ $row->campaign_id }}" data-tm="{{ $row->event_time }}" data-ev="{{ $row->id }}" class="icon-cancel"></a></td> -->
        </tr> 
        @php $x++ @endphp
      @endforeach
    @else
      <tr>
          <td colspan="6" class="text-center">Currently no data available</td>
      </tr>
    @endif
@else <!-- inactive -->
    @if($campaigns->count() > 0)
      @php $x =1 @endphp
      @foreach($campaigns as $row)
        <tr>
          <td class="text-center">{{ $x }}</td>
          @if($is_event == 1)
            <td class="text-center">{{ $row->event_time }}</td>
            <td class="text-center">H{{ $row->days }}</td>
          @endif
          @if($is_event == 0)
            <td class="text-center">H+{{ abs($row->days) }}</td>
          @endif
          <td class="text-center">{{ $row->name }}</td>
          <td class="text-center">{{ $row->telegram_number }}</td>
          <td colspan="2" class="text-center">
            @if($row->status == 1)
              Success
            @elseif($row->status == 2)
              <span class="act-tel-apt-create">Phone Offline</span> 
            @elseif($row->status == 3)
              <span class="act-tel-apt-create">Phone Not Available</span>
            @else
              <span class="act-tel-apt-create">Cancelled</span>
            @endif
          </td>
        </tr> 
        @php $x++ @endphp
      @endforeach
    @else
      <tr>
          <td colspan="6" class="text-center">Currently no data available</td>
      </tr>
    @endif
@endif