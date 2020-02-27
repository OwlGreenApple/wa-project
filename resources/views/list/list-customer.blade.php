@if($contact->count() > 0)
 @php $x=1;  @endphp
  @foreach($contact as $rows)
    <tr>
      <td>{{$x}}</td>
      <td>{{$rows->name}}</td>
      <td>{{$rows->telegram_number}}</td>
      <td>{{$rows->username}}</td>
      <td>{{$rows->email}}</td>
    </tr>
    @php $x++; @endphp
  @endforeach
@endif