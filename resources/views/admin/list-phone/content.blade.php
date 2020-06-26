<?php 
use App\User;
?>
@foreach($phone_numbers as $phone_number)
  <tr>
    <td data-label="Email">
     {{$phone_number->email}}
    </td>
    <td data-label="Username">
      {{$phone_number->phone_number}}
    </td>
    <td data-label="Counter">
      {{$phone_number->counter}}
    </td>
    <td data-label="Created">
      {{$phone_number->created_at}}
    </td>
    <td data-label="Key Woowa">
      {{$phone_number->filename}}
    </td>
    <td>
      <a href="{{url('take-screenshoot').'/'.$phone_number->phone_number}}" target="_blank">open</a>
    </td>
  </tr>
@endforeach