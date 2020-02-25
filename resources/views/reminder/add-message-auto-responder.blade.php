@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>ADD REMINDER : <color>Test Campaigns</color></h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-campaign">
  <form>
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Type Campaign :</label>
        <div class="col-sm-9 py-2">
          <strong>Auto Responder</strong>
        </div>
      </div>

      <div class="form-group row lists">
        <label class="col-sm-3 col-form-label">Select List :</label>
        <div class="col-sm-9 relativity">
           <select name="list_id" class="custom-select-campaign form-control">
              @if($lists->count() > 0)
                @foreach($lists as $row)
                  <option value="{{$row->id}}">{{$row->label}}</option>
                @endforeach
              @endif
           </select>
           <span class="icon-carret-down-circle"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Time to send Message :</label>
        <div class="col-sm-9 relativity inputh">
          <div class="row">
            <select name="day" class="form-control col-sm-7 ml-3 mr-2">
              @for($x=1; $x<=100 ;$x++)
                <option value="{{ $x }}">{{ $x }} days after event</option>
              @endfor
            </select>
            <input name="hour" type="text" class="timepicker form-control col-sm-3" value="00:00" readonly />
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :</label>
        <div class="col-sm-6">
          <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
        </div>
      </div>

      <div class="text-right col-sm-9">
        <button type="submit" class="btn btn-custom">Save</button>
      </div>

  </form>
</div>

<!-- Table -->
<div class="container act-tel-campaign">
    <table class="table table-bordered mt-4">
      <thead class="bg-dashboard">
        <tr>
          <th class="text-center" style="width : 100px">Reminder Time</th>
          <th class="text-center" style="width : 100px">Time Sending</th>
          <th class="text-center">Reminder Messages</th>
          <th class="text-center" style="width : 60px">Edit</th>
          <th class="text-center" style="width : 60px">Delete</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">H-1</td>
          <td class="text-center">00:00</td>
          <td>Remindered Message H-1</td>
          <td class="text-center"><a class="icon icon-edit"></a></td>
          <td class="text-center"><a class="icon icon-delete"></a></td>
        </tr>
      </tbody>
    </table>
</div>

<script type="text/javascript">
  $(function () {
      $("#divInput-description-post").emojioneArea({
          pickerPosition: "right",
          mainPathFolder : "{{url('')}}",
      });
  });

  $(document).ready(function(){
    MDTimepicker();
    neutralizeClock();
  });

    function MDTimepicker(){
      $("body").on('focus','.timepicker',function(){
          $(this).mdtimepicker({
            format: 'hh:mm',
          });
      });
    }

    /* prevent empty col if user click cancel on clock */
    function neutralizeClock(){
       $("body").on("click",".mdtp__button.cancel",function(){
          $(".timepicker").val('00:00');
      });
    }
</script>
@endsection
