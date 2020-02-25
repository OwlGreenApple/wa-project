@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>CREATE CAMPAIGN</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-campaign">
  <form id="save_campaign">
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Name :</label>
        <div class="col-sm-6">
          <input type="text" name="campaign_name" class="form-control" />
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Campaign :</label>
        <div class="col-sm-9">
          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign_type" id="inlineRadio1" value="event" checked>
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio1">Event</label>
          </div>

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign_type" id="inlineRadio2" value="auto">
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio2">Auto Responder</label>
          </div>

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign_type" id="inlineRadio3" value="broadcast">
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio3">Broadcast</label>
          </div>
          <!-- -->
        </div>
      </div>

      <div class="form-group row broadcast-type">
        <label class="col-sm-3 col-form-label">Broadcast Type :</label>
        <div class="col-sm-9 relativity">
           <select name="broadcast_schedule" id="broadcast-schedule" class="custom-select-campaign form-control">
              <option value="0">Schedule Broadcast</option>
              <option value="1">Schedule Group</option>
              <option value="2">Schedule Channel</option>
           </select>
           <span class="icon-carret-down-circle"></span>
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

      <div class="box-schedule"></div>

      <div class="form-group row date-send">
        <label class="col-sm-3 col-form-label">Date Send :</label>
        <div class="col-sm-9 relativity">
          <input id="datetimepicker-date" type="text" name="date_send" class="form-control custom-select-campaign" />
          <span class="icon-calendar"></span>
        </div>
      </div>

      <div class="form-group row event-time">
        <label class="col-sm-3 col-form-label">Event Time :</label>
        <div class="col-sm-9 relativity">
          <input id="datetimepicker" type="text" name="event_time" class="form-control custom-select-campaign" />
          <span class="icon-calendar"></span>
        </div>
      </div>

      <div class="form-group row reminder">
        <label class="col-sm-3 col-form-label">Select Reminder :</label>
        <div class="col-sm-9 relativity">
           <select name="schedule" id="schedule" class="custom-select-campaign form-control">
              <option value="0">The Day</option>
              <option value="1">H-</option>
              <option value="2">H+</option>
           </select>
           <span class="icon-carret-down-circle"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Time to send Message :</label>
        <div class="col-sm-9 relativity inputh">
          <input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" />
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :</label>
        <div class="col-sm-6">
          <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
        </div>
      </div>

      <div class="text-right col-sm-9">
        <button type="submit" class="btn btn-custom">Create</button>
      </div>

  </form>
</div>

<script type="text/javascript">

   /* Datetimepicker */
   $(function () {
        $('#datetimepicker').datetimepicker({
          format : 'YYYY-MM-DD HH:mm',
        }); 

        $('#datetimepicker-date').datetimepicker({
          format : 'YYYY-MM-DD',
        });

        $("#divInput-description-post").emojioneArea({
            pickerPosition: "right",
            mainPathFolder : "{{url('')}}",
        });
    });

  $(document).ready(function(){
    displayOption();
    displayAddDaysBtn();
    MDTimepicker();
    neutralizeClock();
    saveCampaign();
    broadcastSchedule();
  });

  function saveCampaign()
  {
    $("#save_campaign").submit(function(e){
      e.preventDefault();
      var data = $(this).serialize();

      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : '{{url("save-campaign")}}',
          data : data,
          dataType : 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            alert(result.message);
          },
          error : function(xhr,attribute,throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
          }
      });
      //ajax
    });
  }

  function displayOption(){
    $("input[name='campaign_type']").change(function(){
        var val = $(this).val();
        var hday = '<input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />';
        var hplus = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3"><?php for($x=1;$x<=100;$x++) {
                echo "<option value=".$x.">$x days after event</option>";
          }?></select>'+
          '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
          ;

        if(val == 'event')
        {
          $("input[name=event_time]").prop('disabled',false);
          $("input[name=day_reminder]").prop('disabled',false);
          $(".event-time").show();
          $(".reminder").show();
          $(".broadcast-type").hide();
          $(".date-send").hide();
        }
        else if(val == 'auto'){
          $("input[name=event_time]").prop('disabled',true);
          $(".event-time").hide();
          $("input[name=day_reminder]").prop('disabled',false);
          $(".reminder").hide();
          $(".inputh").html(hplus);
          $(".broadcast-type").hide();
          $(".date-send").hide();
        }
        else {
          $("input[name=event_time]").prop('disabled',true);
          $("input[name=day_reminder]").prop('disabled',true);
          $(".event-time").hide();
          $(".reminder").hide();
          $(".inputh").html(hday);
          $(".broadcast-type").show();
          $(".date-send").show();
        }

    });
  }

  function broadcastSchedule()
  {
      $(".broadcast-type").hide();
      $(".date-send").hide();

      $("#broadcast-schedule").change(function(){
        var val = $(this).val();
        var box = '';

        if(val == 0)
        {
            $(".lists").show();
            $("select[name='list_id']").prop('disabled',false);
            $(".box-schedule").html('');
        }
        else if(val == 1)
        {
            $(".lists").hide();
            $("select[name='list_id']").prop('disabled',true);

            box += '<div class="form-group row">';
            box += '<label class="col-sm-3 col-form-label">Telegram Group Name :</label>';
            box += '<div class="col-sm-9 relativity">';
            box += '<input type="text" name="group_name" class="form-control" />';
            box += '</div>';
            box += '</div>';
            $(".box-schedule").html(box);
        }
        else {
            $(".lists").hide();
            $("select[name='list_id']").prop('disabled',true);

            box += '<div class="form-group row">';
            box += '<label class="col-sm-3 col-form-label">Telegram Channel Name :</label>';
            box += '<div class="col-sm-9 relativity">';
            box += '<input type="text" name="channel_name" class="form-control" />';
            box += '</div>';
            box += '</div>';
            $(".box-schedule").html(box);
        }
      });
  }

   function displayAddDaysBtn()
     {
        $(".add-day").hide();
        $("#schedule").change(function(){
          var val = $(this).val();

          var hday = '<input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />';

          var hmin = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3"><?php for($x=-90;$x<=-1;$x++) {
                echo "<option value=".$x.">$x days before event</option>";
          }?></select>'+
          '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
          ;

          var hplus = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3"><?php for($x=1;$x<=100;$x++) {
                echo "<option value=".$x.">$x days after event</option>";
          }?></select>'+
          '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
          ;

          if(val == 0){
            $(".inputh").html(hday);
          } else if(val == 1) {
             $(".inputh").html(hmin);
          } else {
             $(".inputh").html(hplus);
          }

        });
     }

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
