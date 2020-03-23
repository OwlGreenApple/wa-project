@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>Create For Appointment</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-apt">
  <div id="error_db" class="col-lg-12"></div>
  <form id="save_apt">
      
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

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Name Of Appointment :</label>
        <div class="col-sm-9">
          <input type="text" name="name_app" class="form-control" />
          <span class="error name_app"></span>
        </div>
      </div>

      <div class="text-right">
        <button class="btn btn-custom">Create Appointment</button>
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
            pickerPosition: "top",
        });
    });

  $(document).ready(function(){
    //displayOption();
    //displayAddDaysBtn();
    //MDTimepicker();
    //neutralizeClock();
    saveAppointment();
    //broadcastSchedule();
    //showReminder();
  });

  function saveAppointment()
  {
    $("#save_apt").submit(function(e){
      e.preventDefault();
      var data = $(this).serialize();
      var id;

      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : '{{url("save-apt")}}',
          data : data,
          dataType : 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){

            if(result.success == 0)
            {  
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".error").show();
                $(".error_db").html(result.message);
                //$(".list_id").html(result.list_id);
            }
            else
            {
                $(".error").hide();
                id = result.id;
                alert(result.message);
                location.href='{{ url("edit-apt") }}/'+id;
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

  function showReminder()
  {
      $(".board").click(function(){
        var id = $(this).attr('id');

        $("."+id).slideToggle(1000);
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
