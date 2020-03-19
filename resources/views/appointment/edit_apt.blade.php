@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>Add Message To Appointment</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-apt">
 
  <div class="create bg-dashboard">

    <form class="aptform">
     <div class="form-group row reminder">
        <label class="col-sm-3 col-form-label">Choose Reminder Time :</label>
        <div class="col-sm-9 relativity">
           <select name="schedule" id="schedule" class="custom-select-campaign form-control">
              <option value="0">The Day</option>
              <option value="1">H-</option>
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
          <span class="error day"></span><br/>
          <span class="error hour"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :</label>
        <div class="col-sm-9">
          <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
          <span class="error msg"></span>
        </div>
      </div>

      <div class="text-right col-sm-12">
        <button type="submit" class="btn btn-custom">Create Reminder</button>
      </div>
    </form>

    <div class="aptform">
      <div class="col-lg-12">
          <div id="board-1" class="board">
            <div class="left">Reminder H-1</div>
            <div class="right">
              <a class="icon icon-edit"></a>
              <a class="icon icon-delete"></a>
              <a class="icon icon-carret-down-circle"></a>
            </div>
            <div class="clearfix"></div>
          </div>

          <div class="board-1 slide_message">
              Message Reminder H-1
          </div>
      </div>
    </div>

  </div>
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
            pickerPosition: "top",
        });
    });

  $(document).ready(function(){
    displayOption();
    displayAddDaysBtn();
    MDTimepicker();
    neutralizeClock();
    saveCampaign();
    broadcastSchedule();
    showReminder();
  });

  function showReminder()
  {
      $(".board").click(function(){
        var id = $(this).attr('id');

        $("."+id).slideToggle(1000);
      });
  }

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
                alert(result.message);
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
          //$(".broadcast-type").hide();
          $(".date-send").hide();
        }
        else if(val == 'auto'){
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
          $(".inputh").html(hday);
          //$(".broadcast-type").show();
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
