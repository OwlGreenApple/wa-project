  @if(count($events)==0)
    <tr>
      <td colspan="5" class="text-center">
        No data to display
      </td>
    </tr>
  @endif
  @foreach($events as $event)
    <tr>
      <td class="text-center">H{{$event->days}}</td>
      <td class="text-center">{{$event->hour_time}}</td>
      <td>{{$event->message}}</td>
      <td class="text-center"><a class="icon icon-edit" data-id="{{$event->id}}" data-list_id="{{$event->list_id}}" data-campaign_id="{{$event->campaign_id}}" data-days="{{$event->days}}" data-event_time="{{$event->event_time}}" data-hour_time="{{$event->hour_time}}" data-message="{{$event->message}}" ></a></td>
      <td class="text-center"><a class="icon icon-delete" data-toggle="modal" data-target="#confirm-delete" data-id="{{$event->id}}" ></a></td>
    </tr>
  @endforeach