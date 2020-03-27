@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h3 class="campaign">Add Message To : <color>{{ $campaign_name }}</color></h3>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-apt">
  <div id="status_db"><!-- display status --></div>
  <div class="create bg-dashboard">

    <form id="save_template_apt" class="aptform">
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
          <textarea name="message" id="divInput-description-post" class="form-control text-left"></textarea>
          <span class="error msg"></span>
        </div>
      </div>

      <div class="text-right col-sm-12">
        <button type="submit" id="btn-submit" class="btn btn-custom">Create Reminder</button>
        <button type="button" id="cancel" class="btn btn-danger">Cancel</button>
      </div>
    </form>

    <div id="display_reminder_apt"><!-- display reminder --></div>

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
            mainPathFolder: "{{ url('/assets') }}",
        });
    });

  $(document).ready(function(){
    displayAppointment();
    saveAppointment();
    showReminder();
    deleteAppointment();
    displayEditTemplateAppt();
    displayAddDaysBtn();
    MDTimepicker();
    neutralizeClock();
    cancelUpdate();
  });

  function cancelUpdate()
  {
      $("#cancel").hide();
      $("#cancel").click(function(){
        $('#btn-submit').html('Create Reminder').removeAttr('data-update');
        $("select[name='schedule'] > option[value='0']").prop('selected',true);
        buttonTime(0);
        $("input[name='hour']").val('00:00');
        $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('');
        $(this).hide();
      });
  }

  function displayEditTemplateAppt()
  {
      $("body").on("click",".icon-edit",function(){
        var id = $(this).attr('id');

        $.ajax({
          type : 'GET',
          url : '{{ url("edit-appt-template") }}',
          data : {id : id},
          dataType : 'json',
         /* beforeSend : function(){
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },*/
          success : function(result)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $('#btn-submit').html('Update Reminder').attr('data-update',id);
            $("#cancel").show();

            if(result.day < 0)
            {
                $("select[name='schedule'] > option[value='1']").prop('selected',true);
                buttonTime(1,result.day);
                $("input[name='hour']").val(result.time_send);
            }
            else
            {
                $("select[name='schedule'] > option[value='0']").prop('selected',true);
                buttonTime(0);
                $("input[name='hour']").val(result.time_send);
            }

            $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(result.msg);
            $('html, body').animate({
                scrollTop: $(".create ").offset().top
            }, 1000);
          },
          error : function(xhr)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
        });
      });
  }

  function deleteAppointment()
  {
      $("body").on('click','.icon-delete',function(){
          var id = $(this).attr('id');
          var notif = confirm('Are you sure want to delete this appointment reminder?');

          if(notif == true)
          {
              executeDeleteTemplateAppt(id)
          }
          else
          {
              return false;
          }
      });
  }

  function executeDeleteTemplateAppt(id)
  {
      $.ajax({
        type : 'GET',
        url : '{{ url("delete-appt-template") }}',
        data : {id : id},
        dataType : 'json',
        beforeSend : function(){
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert(result.message);
          if(result.success == 1)
          {
              displayAppointment();
          }
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
  }

  function saveAppointment()
  {
    $("#save_template_apt").submit(function(e){
      e.preventDefault();
      var is_update = $("#btn-submit").attr('data-update');
      var data = $(this).serializeArray();
      data.push({name:'campaign_id',value : {!! $id !!} },{name : 'is_update', value : is_update});

      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : '{{url("save-template-appoinments")}}',
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

            if(result.success == 0)
            {  
                $(".error").show();
                $(".day").html(result.day);
                $(".hour").html(result.hour);
                $(".msg").html(result.msg);
                $("#status_db").html(result.message);
                $("#status_db").hide();
            }
            else
            {
                $(".error").hide();
                $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('');
                $("#status_db").attr('class','status');
                $("#status_db").html(result.message);
                $("#cancel").trigger("click");
                displayAppointment();
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

  function displayAppointment()
  {
      $.ajax({
        type : 'GET',
        url : '{{ url("display-template-apt") }}',
        data : {id : {!! $id !!}},
        dataType : 'html',
        beforeSend : function(){
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#display_reminder_apt").html(result);
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
  }

  function showReminder()
  {
      $("body").on('click','.icon-carret-down-circle',function(){
        var id = $(this).attr('id');
        $(".board-"+id).slideToggle(1000);
      });
  }

  function displayAddDaysBtn()
   {
      $(".add-day").hide();
      $("#schedule").change(function(){
          var val = $(this).val();
          buttonTime(val);
      });
   }

  function buttonTime(val,day)
  {
        var hmin = '';
        var hday = '<input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />';
        var options = '';

        for(x=-1;x>=-90;x--){
          if(x == day)
          {
            options += '<option value='+x+' selected>'+x+' days before appointment</option>';
          }
          else
          {
            options += '<option value='+x+'>'+x+' days before appointment</option>';
          }
        }

       hmin += '<select name="day" class="form-control col-sm-7 float-left days mr-3">';
       hmin += options;
       hmin +='</select>';
       hmin += '<input name="hour" type="text" class="timepicker form-control col-sm-4" value="00:00" readonly />';

        if(val == 0){
          $(".inputh").html(hday);
        } else if(val == 1) {
           $(".inputh").html(hmin);
        }
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
