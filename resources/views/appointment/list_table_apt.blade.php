@if($campaigns->count() > 0)
  {{ $x =1 }}
  @foreach($campaigns as $row)
    <tr>
      <td class="text-center">{{ $x }}</td>
      <td class="text-center">{{ $row->event_time }}</td>
      <td class="text-center">{{ $row->name }}</td>
      <td class="text-center">{{ $row->telegram_number }}</td>
      <td class="text-center">
        <a id="{{ $row->campaign_id }}" data-ev="" data-name="{{ $row->name }}" data-phone="{{ $row->telegram_number }}" data-customer-id="{{ $row->id }}" class="icon-edit"></a>
      </td>
      <td class="text-center"><a id="{{ $row->campaign_id }}" data-tm="{{ $row->event_time }}" data-ev="{{ $row->id }}" class="icon-cancel"></a></td>
    </tr> 
    {{ $x++ }}
  @endforeach
@else
  <tr>
      <td colspan="6" class="text-center">Currently no data available</td>
  </tr>
@endif