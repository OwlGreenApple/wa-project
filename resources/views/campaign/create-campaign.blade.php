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
      <input type="hidden" name="campaign_id" value="new">
      <input type="hidden" name="reminder_id" value="new">
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Name :</label>
        <div class="col-sm-6">
          <input type="text" name="campaign_name" class="form-control" />
          <span class="error campaign_name"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Campaign :
                    <span class="tooltipstered" title="<div class='panel-heading'>Campaign type</div><div class='panel-content'>
										Broadcast <br>
										Auto Responder <br>
										Event
                    </div>">
                      <i class="fa fa-question-circle "></i>
                    </span></label>
        <div class="col-sm-9">

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign_type" id="inlineRadio3" value="broadcast" checked/>
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio3">Broadcast</label>
          </div>

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign_type" id="inlineRadio2" value="auto" />
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio2">Auto Responder</label>
          </div>

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign_type" id="inlineRadio1" value="event" />
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio1">Event</label>
          </div>

          <div class="form-check-inline">
            <label class="custom-radio">
              <a class="fa fa-question-circle" aria-hidden="true"></a>
            </label>
          </div>
          
          <!-- -->
        </div>
      </div>

      <!--
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
      -->

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
           <span class="error list_id"></span>
        </div>
      </div>

      <!-- <div class="box-schedule"></div> -->

      <div class="form-group row date-send">
        <label class="col-sm-3 col-form-label">Date Send :</label>
        <div class="col-sm-9 relativity">
          <input id="datetimepicker-date" type="text" name="date_send" class="form-control custom-select-campaign" />
          <span class="icon-calendar"></span>
          <span class="error date_send"></span>
        </div>
      </div>

      <div class="form-group row event-time">
        <label class="col-sm-3 col-form-label">Event Time :</label>
        <div class="col-sm-9 relativity">
          <input id="datetimepicker" type="text" name="event_time" class="form-control custom-select-campaign" />
          <span class="icon-calendar"></span>
          <span class="error event_time"></span>
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
        <div class="col-sm-9 relativity">
          <span class="inputh">
            <input name="hour" type="text" class="timepicker form-control" value="00:00" readonly />
          </span>
          <!-- <span class="inputh">
             <select name="day" class="form-control col-sm-7 float-left days delcols mr-3"> @for($x=1;$x<=100;$x++) 
                  <option value="{{ $x }}">{{ $x }} days after event</option>;
             @endfor
             </select>
            <input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />
          </span> -->
          <span class="error day"></span><br/>
          <span class="error hour"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :
					<span class="tooltipstered" title="<div class='panel-heading'>Message</div><div class='panel-content'>
						You can use this as 'Personalization field' <br>
						[NAME] <br>
						[PHONE] <br>
						[WA] <br>
						</div>">
						<i class="fa fa-question-circle "></i>
					</span>
				</label>
        <div class="col-sm-6">
          <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
          <span class="error msg"></span>
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
          minDate: new Date()
        }); 

        $('#datetimepicker-date').datetimepicker({
          minDate: new Date(), 
          format : 'YYYY-MM-DD',
        });

        $("#divInput-description-post").emojioneArea({
            pickerPosition: "right",
            mainPathFolder : "{{url('')}}",
        });
    });

  $(document).ready(function(){
    displayOption();
    openingPageType();
    displayAddDaysBtn();
    MDTimepicker();
    neutralizeClock();
    saveCampaign();
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

            if(result.err == 'ev_err')
            {  
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".error").show();
                $(".campaign_name").html(result.campaign_name);
                $(".list_id").html(result.list_id);
                $(".event_time").html(result.event_time);
                $(".day").html(result.day);
                $(".hour").html(result.hour);
                $(".msg").html(result.msg);
            }
            else if(result.err == 'responder_err')
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".error").show();
                $(".campaign_name").html(result.campaign_name);
                $(".list_id").html(result.list_id);
                $(".day").html(result.day);
                $(".hour").html(result.hour);
                $(".msg").html(result.msg);
            }
            else if(result.err == 'broadcast_err')
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".error").show();
                $(".campaign_name").html(result.campaign_name);
                $(".list_id").html(result.list_id);
                $(".group_name").html(result.group_name);
                $(".channel_name").html(result.channel_name);
                $(".date_send").html(result.date_send);
                $(".hour").html(result.hour);
                $(".msg").html(result.msg);
            }
            else
            {
                $(".error").hide();
                $("input[name='campaign_name']").val('');
                $("input[name='group_name']").val('');
                $("input[name='group_name']").val('');
                $("input[name='channel_name']").val('');
                $("input[name='date_send']").val('');
                $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('');
                // alert(result.message);
                location.href='{{ url("campaign") }}';
            }
          },
          error : function(xhr,attribute,throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
      });
      //ajax
    });
  }

  function displayOption(){
    $("input[name='campaign_type']").on('change',function(){
        var val = $(this).val();
        displayFormCampaign(val);
    });
  }

  function openingPageType()
  {
    var radio_option = $("input[name='campaign_type'] checked").val();
    console.log(radio_option);
    displayFormCampaign(radio_option);
  }

  function displayFormCampaign(val)
  {
      var hday = '<input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />';


      if(val == 'event')
      {
				var hplus = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3"><?php for($x=1;$x<=100;$x++) {
              echo "<option value=".$x.">$x days after event</option>";
        }?></select>'+
        '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
        ;

        $("input[name=event_time]").prop('disabled',false);
        $("input[name=day_reminder]").prop('disabled',false);
        $(".event-time").show();
        $(".reminder").show();
        $(".inputh").html(hplus);
        //$(".broadcast-type").hide();
        $(".date-send").hide();
      }
      else if(val == 'auto'){
				var hplus = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3"><?php for($x=1;$x<=100;$x++) {
              echo "<option value=".$x.">$x days after subscribed</option>";
        }?></select>'+
        '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
        ;

        $("input[name=event_time]").prop('disabled',true);
        $(".event-time").hide();
        $("input[name=day_reminder]").prop('disabled',false);
        $(".reminder").hide();
        $(".inputh").html(hplus);
        //$(".broadcast-type").hide();
        $(".date-send").hide();
      }
      else {
        $("input[name=event_time]").prop('disabled',true);
        $("input[name=day_reminder]").prop('disabled',true);
        $(".event-time").hide();
        $(".reminder").hide();
        $(".date-send").show();
        $(".inputh").html(hday);
        //$(".broadcast-type").show();
      }
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

  /*function broadcastSchedule()
  {
      $(".broadcast-type").hide();

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
            box += '<span class="error group_name"></span>';
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
            box += '<span class="error channel_name"></span>';
            box += '</div>';
            box += '</div>';
            $(".box-schedule").html(box);
        }
      });
  }*/
</script>
@endsection
