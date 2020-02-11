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
      if ($phoneNumber->status == 0) {
        echo "Pending";
      }
      if ($phoneNumber->status == 1) {
        echo 'Waiting Verification <a href="#" class="link-verify" data-phone="'.$phoneNumber->phone_number.'">(klik to verify)</a>';
      }
      if ($phoneNumber->status == 2) {
        echo "Connected";
      }
      ?></td>
      <td class="text-center"><a class="icon icon-delete" data-id="{{$phoneNumber->id}}"></a></td>
    </tr>
  @endforeach