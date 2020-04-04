  @if(count($phoneNumbers)==0)
    <tr>
      <td colspan="4" class="text-center">
        No data to display
      </td>
    </tr>
  @endif
  @foreach($phoneNumbers as $phoneNumber)
    <tr>
      <td class="text-center">1</td>
      <td class="text-center">{{$phoneNumber->phone_number}}</td>
      <td class="text-center"><?php 
      if (($phoneNumber->status == 0) || ($phoneNumber->status == 1)) {
        echo '<a href="#" class="link-verify btn btn-success btn-sm" data-phone="'.$phoneNumber->phone_number.'">klik to verify</a>';
      }
      if ($phoneNumber->status == 2) {
        echo "Server Connected";
      }
      ?></td>
      <!-- <td class="text-center"><a class="icon icon-edit btn-edit" data-number="{{$phoneNumber->phone_number}}"></a></td> -->
      <td class="text-center">
        <a class="icon icon-delete" data-id="{{$phoneNumber->id}}"></a>
      </td>
    </tr>
  @endforeach