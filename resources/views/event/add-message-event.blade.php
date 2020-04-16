@extends('layouts.app')

@section('content')
<!-- Modal Delete Confirmation -->
<div class="modal fade" id="confirm-delete" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content content-premiumid">
      <div class="modal-header header-premiumid">
        <h5 class="modal-title" id="modaltitle">
          Confirmation Delete
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <input type="hidden" name="id_reminder" id="id_reminder">

        <label>Are you sure want to <i>delete</i> this data ?</label>
        <br><br>
        <span class="txt-mode"></span>
        <br>
        
        <div class="col-12 mb-4" style="margin-top: 30px">
          <button class="btn btn-danger btn-block btn-delete-ok" data-dismiss="modal" id="button-delete-reminder">
            Yes, Delete Now
          </button>
        </div>
        
        <div class="col-12 text-center mb-4">
          <button class="btn  btn-block btn-delete-ok" data-dismiss="modal">
            Cancel
          </button>  
        </div>
      </div>
    </div>   
  </div>
</div>

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>ADD MESSAGE</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-campaign">
  <form id="save_campaign">
      <input type="hidden" name="campaign_type" value="event">
      <input type="hidden" name="campaign_id" value="<?php echo $campaign_id; ?>">
      <input type="hidden" name="reminder_id" value="new">
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Type Campaign :</label>
        <div class="col-sm-9 py-2">
          <strong>Event</strong>
        </div>
      </div>

      <div class="form-group row lists">
        <label class="col-sm-3 col-form-label">Current List :</label>
        <div class="col-sm-9 relativity">
          {{ $currentlist }}
           <!-- <select name="list_id" class="custom-select-campaign form-control">
              @if($lists->count() > 0)
                @foreach($lists as $row)
                  <option value="{{$row->id}}">{{$row->label}}</option>
                @endforeach
              @endif
           </select> 
           <span class="icon-carret-down-circle"></span>
           -->
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Campaign Name :</label>
        <div class="col-sm-6">
          <div>{{ $campaign_name }}</div>
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
          <div class="inputh">
            <input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" />
          </div>
          <span class="error day"></span>
          <span class="error hour"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :</label>
        <div class="col-sm-6">
          <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
          <span class="error message"></span>
        </div>
      </div>

      <div class="text-right col-sm-9">
        <button type="submit" class="btn btn-custom">Save</button>
        <button type="button" id="btn-clear" class="btn btn-custom">Clear</button>
      </div>

			<div class="form-group row">
			<label class="col-sm-3 col-form-label">Send 1 test Message</label>
			<div class="col-sm-9 relativity">
					<input type="text" id="phone" name="phone_number" class="form-control" />
					<span class="error code_country"></span>
					<span class="error phone_number"></span>
					<button type="button" class="btn btn-test">Send Test</button>
			</div>
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

      <tbody id="tbody-event">
      </tbody>
    </table>
</div>

<script type="text/javascript">

   /* Datetimepicker + emojione */
  $(function () {
      $('#datetimepicker').datetimepicker({
        format : 'YYYY-MM-DD HH:mm',
        minDate : new Date()
      }); 

      $("#divInput-description-post").emojioneArea({
          pickerPosition: "right",
          //mainPathFolder : "aaaaaa",
      });
  });

  function saveEvent()
  {
    $("#save_campaign").submit(function(e){
      e.preventDefault();
      var data = $(this).serializeArray();
      data.push({name:'list_id',value:'{!! $currentlistid !!}'},{ name:'campaign_name', value:'<?php echo $campaign_name;?>'});

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
            loadEvent();

            if(result.err == 0)
            {
              alert(result.message);
              $("input[name='event_time']").val('')
              $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('');
              $(".error").hide();
            }
            else
            {
              $(".error").show();
              $(".event_time").html(result.event_time);
              $(".day").html(result.day);
              $(".hour").html(result.hour);
              $(".message").html(result.msg);
            }
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
  
  function loadEvent()
  {
    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      type: 'GET',
      url: "<?php echo url('/load-event');?>",
      data : {
        campaign_id : "<?php echo $campaign_id; ?>"
      },
      dataType: 'text',
      beforeSend: function()
      {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');

        var data = jQuery.parseJSON(result);
        $('#tbody-event').html(data.view);
      }
    });
  }

  $(document).ready(function(){
      displayAddDaysBtn();
      MDTimepicker();
      neutralizeClock();
      saveEvent();
      loadEvent();
      clickButtonEdit();
      clickButtonDelete();
      clickIconDelete();
      clickButtonClear();
      $("#btn-clear").hide();
      sendTestMessage();
  });

  function displayAddDaysBtn()
  {
      $(".add-day").hide();
      $("#schedule").change(function(){
        var val = $(this).val();

        var hday = '<input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />';

        var hmin = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3">'+'<?php for($x=-90;$x<=-1;$x++) {echo "<option value=".$x.">$x days before event</option>";}?></select><input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />';

        var hplus = '<select name="day" class="form-control col-sm-7 float-left days delcols mr-3">'+'<?php for($x=1;$x<=100;$x++) {echo "<option value=".$x.">$x days after event</option>";}?></select><input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />';

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
  
  function clickButtonEdit(){
    $("body").on('click','.icon-edit',function(e){
      e.preventDefault();
      $('input[name="reminder_id"]').val($(this).attr("data-id"));
      $('input[name="event_time"]').val($(this).attr("data-event_time"));
      if ( $(this).attr("data-days") == 0 ) {
        $('select[name="schedule"]').val(0).trigger('change');
        displayAddDaysBtn();
      }
      else if ( $(this).attr("data-days") > 0 ) {
        $('select[name="schedule"]').val(2).trigger('change');
        $('select[name="day"]').val($(this).attr("data-days")).trigger('change');
        displayAddDaysBtn();
      }
      else if ( $(this).attr("data-days") < 0 ) {
        $('select[name="schedule"]').val(1).trigger('change');
        $('select[name="day"]').val($(this).attr("data-days")).trigger('change');
        displayAddDaysBtn();
      }
      $('input[name="hour"]').val($(this).attr("data-hour_time"));
      $('select[name="list_id"]').val($(this).attr("data-list_id"));
      $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText($(this).attr("data-message"));
      $("#btn-clear").show();
       $('html, body').animate({
          scrollTop: $(".act-tel-campaign ").offset().top
      }, 1000);
    });
  }

  function clickButtonClear(){
    $("body").on('click','#btn-clear',function(e){
      $('input[name="reminder_id"]').val("new");
      $('input[name="event_time"]').val("");
      $('select[name="list_id"]').val("");
      $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('');
      $("#btn-clear").hide();
    });
  }
  
  function clickIconDelete(){
    $("body").on('click','.icon-delete',function(e){
      e.preventDefault();
      $("#id_reminder").val($(this).attr("data-id"));
    });
  }
  
  function clickButtonDelete(){
    $('#button-delete-reminder').click(function(){
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: "<?php echo url('/delete-event');?>",
        data: {
          id : $("#id_reminder").val(),
        },
        dataType: 'text',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          var data = jQuery.parseJSON(result);
          alert(data.message);
          loadEvent();
        }
      });
    });
  }
	
  function sendTestMessage(){
    $("body").on("click",".btn-test",function(){
				$.ajax({
						headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
						type : 'POST',
						url : '{{url("send-test-message")}}',
						data : {
							message : $("#divInput-description-post").emojioneArea()[0].emojioneArea.getText(),
							phone : $(".iti__selected-flag").attr('data-code')+$("#phone").val()
						},
						dataType : 'json',
						beforeSend: function()
						{
							$('#loader').show();
							$('.div-loading').addClass('background-load');
						},
						success : function(result){
							$('#loader').hide();
							$('.div-loading').removeClass('background-load');
							alert("please check your phone");
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

</script>  
<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
@endsection
