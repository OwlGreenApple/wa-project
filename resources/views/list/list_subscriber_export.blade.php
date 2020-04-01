<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>WA Number</th>
      <th>Email</th>
      @if($import == 0)
        <th>Additional</th>
      @endif
    </tr>
  </thead>

@if($customer->count() > 0)
  <tbody>
      <tr><!-- give row space for import --></tr>
      @foreach($customer as $row)
        <tr>
          <td>{{ $row->name }}</td>
          @if(env('APP_ENV') == 'production')
            <td>+{{ $row->telegram_number }}</td>
          @else
            <td>{{ $row->telegram_number }}</td>
          @endif
          <td>{{ $row->email }}</td>
          @if($import == 0)
            <td>
                @php $additional = json_decode($row->additional,true); @endphp
                @if(count($additional) > 0)
                  @foreach($additional as $label=>$value)
                    {{ $label }} = {{ $value }} <br style="mso-data-placement:same-cell;" />
                  @endforeach
                @endif
            </td>
          @endif
        </tr> 
      @endforeach
  </tbody>
@endif
</table>