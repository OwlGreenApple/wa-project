<!-- Queue -->
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
          <!-- <td class="text-center">
            <a id="{{ $row->campaign_id }}" data-ev="{{ $row->event_time }}" data-name="{{ $row->name }}" data-phone="{{ $row->telegram_number }}" data-customer-id="{{ $row->id }}" class="icon-edit"></a>
          </td> -->
          <td class="text-center"><a id="{{ $row->rcid }}" class="icon-cancel"></a></td> 
          <!-- <td class="text-center"><a id="{{ $row->campaign_id }}" data-tm="{{ $row->event_time }}" data-ev="{{ $row->id }}" class="icon-cancel"></a></td> -->
        </tr> 
        @php $x++ @endphp
      @endforeach
    @else
      <tr>
          <td colspan="6" class="text-center">Currently no data available</td>
      </tr>
    @endif
@else <!-- inactive / delivered -->
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
            <td class="text-center"><a class="open_message" data-message="{{ str_replace(array('[NAME]','[PHONE]','[EMAIL]'),array($row->name,$row->telegram_number,$row->email),$row->message) }}" >H+{{ abs($row->days) }}</a></td>
          @endif
          <td class="text-center">{{ Date('M d Y h:i:s A',strtotime($row->updated_at)) }}</td>
          <td class="text-center">{{ $row->name }}</td>
          <td class="text-center">{{ $row->telegram_number }}</td>
          <td colspan="2" class="text-center"> {!! message_status($row->status) !!}</td>
        </tr> 
        @php $x++ @endphp
      @endforeach
    @else
      <tr>
          <td colspan="6" class="text-center">Currently no data available</td>
      </tr>
    @endif
@endif