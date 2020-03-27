@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2 class="campaign">Campaigns</h2>
  </div>

  <div class="act-tel-dashboard-right">
     <a href="{{url('create-campaign')}}" class="btn btn-custom">Create Campaign</a>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
    <div class="row">
      <div class="input-group col-lg-4">
          <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find a campaign by a name" >
          <span class="input-group-append">
            <div class="btn search-icon">
                <span class="icon-search"></span>
            </div>
          </span>
      </div> 

      <div class="col-lg-6"></div>

      <div class="input-group col-lg-2">
         <select id="campaign_option" class="custom-select-campaign form-control col-lg-10 relativity">
            <option value="all">All</option>
            <option value="0">Event</option>
            <option value="1">Auto Responder</option>
            <option value="2">Broadcast</option>
         </select>
         <span class="icon-triangular"></span>
      </div>

      <div class="clearfix"></div>

    </div>
  </div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="act-tel-tab">
      <div id="display_campaign" class="col-lg-12">
        <!-- display campaign -->
      </div>
  </div>
</div>

<!-- Modal Duplicate Event -->
  <div class="modal fade child-modal" id="modal_duplicate" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2 act-tel-campaign">
                  <form id="duplicate">
                    
                    <div class="form-group">
                      <label>Event Name</label>
                      <input type="text" class="form-control" name="campaign_name" />
                      <span class="error campaign_name"></span>
                    </div>

                    <div class="form-group">
                      <label>Event Date & Time</label>
                      <div class="relativity">
                        <input id="datetimepicker" type="text" name="event_time" class="form-control custom-select-campaign" />
                        <span class="icon-calendar"></span>
                      </div>
                      <span class="error event_time"></span>
                    </div>
                 
                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Save</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
               
            </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

  <!-- Modal Duplicate Auto Responder -->
  <div class="modal fade child-modal" id="modal_duplicate_reminder" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2 act-tel-campaign">
                  <form id="duplicate_reminder">
              
                    <div class="form-group">
                      <label>Auto Responder Name</label>
                      <input type="text" class="form-control" name="campaign_name" />
                      <span class="error campaign_name_reminder"></span>
                    </div>
                 
                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Save</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
               
            </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

  <!-- Modal Duplicate Broadast -->
  <div class="modal fade child-modal" id="modal_duplicate_broadcast" role="dialog">
    <div class="modal-dialog" style="max-width : 800px">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2 act-tel-campaign">
                  <form id="duplicate_broadcast">

                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Broadcast Name</label>
                      <div class="col-sm-9 relativity">
                        <input type="text" class="form-control" name="campaign_name" />
                        <span class="error campaign_name"></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Broadcast Type :</label>
                      <div class="col-sm-9 relativity">
                         <div class="broadcast-type from-control"></div>
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
                         <span class="error list_id"></span>
                      </div>
                    </div>

                    <div class="box-schedule"></div>

                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Deliver Date :</label>
                      <div class="col-sm-9 relativity">
                        <input id="datetimepicker-date" type="text" name="date_send" class="form-control custom-select-campaign" />
                        <span class="icon-calendar"></span>
                        <span class="error date_send"></span>
                      </div>
                    </div>

                    <div class="form-group row">
                      <label class="col-sm-3 col-form-label">Time to send Message :</label>
                      <div class="col-sm-9 relativity">
                        <input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" />
                        <span class="error hour"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Message :</label>
                      <div class="col-sm-9">
                        <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
                        <span class="error message"></span>
                      </div>
                    </div>
                 
                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Save</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
               
            </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

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
      displayResult();
      displayCampaign();
      delBroadcast();
      delAutoResponder();
      delEvent();
      searchCampaign();
      duplicateEventForm();
      duplicateEvent();
      duplicateResponderForm();
      duplicateResponder();
      duplicateBroadcastForm();
      duplicateBroadcast();
      MDTimepicker(); 
      neutralizeClock();
  });

  function duplicateEventForm()
  {
    $("body").on("click",".event_duplicate",function(){
        var id = $(this).attr('id');
        $("#duplicate").attr('data',id);
        $("#modal_duplicate").modal();
    });
  }

  function duplicateEvent()
  {
    $("#duplicate").submit(function(e){
        e.preventDefault();
        var campaign_id = $(this).attr('data');
        var option_position = $("#campaign_option").val();

        var data = $(this).serializeArray();
        data.push({name : 'id', value:campaign_id});

        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type: 'POST',
          url: "{{ url('event-duplicate') }}",
          data: data,
          dataType: 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success: function(result) {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            if(result.success == 0)
            { 
              $(".error").show();
              $(".campaign_name").html(result.campaign_name);
              $(".event_time").html(result.event_time);
            }
            else
            {
              $(".error").hide();
              alert(result.message);
              $("#modal_duplicate").modal('hide');
              $("#duplicate:input").val('');

              if(option_position == 'all')
              {
                displayResult();
              }
              else
              {
                displayEvent();
              }
              
            }
          },
          error : function(xhr,attr,throwable){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $(".error").hide();
            alert(xhr.responseText);
          }
      });

    });
  }

  function duplicateResponderForm()
  {
    $("body").on("click",".responder_duplicate",function(){
        var id = $(this).attr('id');
        $("#duplicate_reminder").attr('data',id);
        $("#modal_duplicate_reminder").modal();
    });
  }

  function duplicateResponder()
  {
    $("#duplicate_reminder").submit(function(e){
        e.preventDefault();
        var campaign_id = $(this).attr('data');
        var option_position = $("#campaign_option").val();

        var data = $(this).serializeArray();
        data.push({name : 'id', value : campaign_id});

        $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: "{{ url('reminder-duplicate') }}",
        data: data,
        dataType: 'json',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.success == 0)
          { 
            $("#duplicate_reminder :input").val('');
            $(".error").show();
            $(".campaign_name_reminder").html(result.campaign_name);
          }
          else
          {
            $(".error").hide();
            alert(result.message);
            $("#modal_duplicate_reminder").modal('hide');
            $("#duplicate_reminder :input").val('');

            if(option_position == 'all')
            {
              displayResult();
            }
            else
            {
              displayAutoResponder();
            }        
          }
        },
        error : function(xhr,attr,throwable){
          $(".error").hide();
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert(xhr.responseText);
        }
      });

    });
  }

  function duplicateBroadcastForm()
  {
    $("body").on("click",".broadcast_duplicate",function(){
        var id = $(this).attr('id');

        $.ajax({
          type : 'GET',
          url : '{{ url("broadcast-check") }}',
          data : {id : id},
          dataType : "json",
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            broadcastFormArrange(result);
          },
          error: function(xhr,attr,throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
          }
        });

        $("#duplicate_broadcast").attr('data',id);
        $("#modal_duplicate_broadcast").modal();
    });
  }

  function broadcastFormArrange(result)
  {
      var box = '';
      $("input[name='campaign_name']").val(result.campaign);
      if(result.list_id > 0){
        $(".broadcast-type").html('Schedule Broadcast');
        $(".lists").show();
        $("select[name='list_id']").prop('disabled',false);
        $("select[name='list_id'] > option[value="+result.list_id+"]").prop('selected',true);
        $(".box-schedule").html('');
      }
      else if(result.list_id == 0 && result.group_name !== null)
      {
        $(".broadcast-type").html('Schedule Group');
        $(".lists").hide();
        $("select[name='list_id']").prop('disabled',true);

        box += '<div class="form-group row">';
        box += '<label class="col-sm-3 col-form-label">Telegram Group Name :</label>';
        box += '<div class="col-sm-9 relativity">';
        box += '<input type="text" value="'+result.group_name+'" name="group_name" class="form-control" />';
        box += '<span class="error group_name"></span>';
        box += '</div>';
        box += '</div>';
        $(".box-schedule").html(box);
      }
      else if(result.list_id == 0 && result.channel !== null)
      {
        $(".broadcast-type").html('Schedule Channel');
        $(".lists").hide();
        $("select[name='list_id']").prop('disabled',true);

        box += '<div class="form-group row">';
        box += '<label class="col-sm-3 col-form-label">Telegram Channel Name :</label>';
        box += '<div class="col-sm-9 relativity">';
        box += '<input type="text" value="'+result.channel+'" name="channel_name" class="form-control" />';
        box += '<span class="error channel_name"></span>';
        box += '</div>';
        box += '</div>';
        $(".box-schedule").html(box);
      }

      $("input[name='date_send']").val(result.day_send);
      $("input[name='hour']").val(result.hour_time);
      $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(result.message);
  }

  function duplicateBroadcast()
  {
    $("#duplicate_broadcast").submit(function(e){
      e.preventDefault();
      var reminder_id = $(this).attr('data');
      var option_position = $("#campaign_option").val();

      var data = $(this).serializeArray();
      data.push({name : 'id', value : reminder_id});

      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: "{{ url('broadcast-duplicate') }}",
        data: data,
        dataType: 'json',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(result.success == 0)
          {
            $(".error").show();
            $(".campaign_name").html(result.campaign_name);
            $(".group_name").html(result.group_name);
            $(".channel_name").html(result.channel_name);
            $(".date_send").html(result.date_send);
            $(".hour").html(result.hour);
            $(".message").html(result.message);
            $(".list_id").html(result.list_id);
          }
          else
          {
            $(".error").hide();
            alert(result.message);
            $("#modal_duplicate_broadcast").modal('hide');
            $("#duplicate_broadcast:input").val('');

            if(option_position == 'all')
            {
              displayResult();
            }
            else
            {
              displayBroadcast();
            }
          }
        },
        error : function(xhr,attr,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert(xhr.responseText);
        }
      });

    });
  }

  function displayCampaign() {
      $("#campaign_option").change(function(){
          var val = $(this).val();

          if(val == 'all')
          {
            displayResult();
          }
          else if(val == 0){
            displayEvent();
          }
          else if(val == 1)
          {
            displayAutoResponder();
          }
          else 
          {
            displayBroadcast();
          }
          
      });
  }

  function displayEvent()
  {
     $.ajax({
        type : 'GET',
        url : '{{ route("eventlist") }}',
        data : {type : 0},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#display_campaign").html(result);
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        }
     });
  }

  function displayBroadcast()
  {
    $.ajax({
        type : 'GET',
        url : '{{ route("broadcastlist") }}',
        data : {type : 2},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#display_campaign").html(result);          
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert(xhr.responseText);
        }
    });
  }

  function displayAutoResponder()
  {
    $.ajax({
        type : 'GET',
        url : '{{ route("reminderlist") }}',
        data : {type : 1},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');         
          $("#display_campaign").html(result); 
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
    });
  }

  function delBroadcast()
  {
    $("body").on("click",".broadcast-del",function(){
      var id = $(this).attr('id');
      var option_position = $("#campaign_option").val();
      var conf = confirm("Are you sure to delete this broadcast?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("broadcast-del") }}',
          data : {
              id : id,
              //mode : "broadcast"
          },
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            alert(result.message);
            if(option_position == 'all')
            {
              displayResult();
            }
            else
            {
              displayBroadcast();
            }
          },
          error : function(xhr, attr, throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
        });
      }
      else 
      {
        return false;
      }
    });
  }

  function delAutoResponder()
  {
    $("body").on("click",".responder-del",function(){
      var id = $(this).attr('id');
      var option_position = $("#campaign_option").val();
      var conf = confirm("Are you sure to delete this auto responder?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("campaign-del") }}',
          data : {
            id : id,
            mode : "auto_responder"
          },
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            alert(result.message);
            if(option_position == 'all')
            {
              displayResult();
            }
            else
            {
              displayAutoResponder();
            }
          },
          error : function(xhr, attr, throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
        });
      }
      else 
      {
        return false;
      }
    });
  }

  function delEvent()
  {
    $("body").on("click",".event-del",function(){
      var id = $(this).attr('id');
      var option_position = $("#campaign_option").val();
      var conf = confirm("Are you sure to delete this event?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("campaign-del") }}',
          data : {
            id : id,
            mode : "event"
          },
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            alert(result.message);

            if(option_position == 'all')
            {
              displayResult();
            }
            else
            {
              displayEvent();
            }
          },
          error : function(xhr, attr, throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
        });
      }
      else 
      {
        return false;
      }
    });
  }

  function searchCampaign()
  {
      $(".search-icon").click(function(){
        var search = $(".search-box").val();
        displayResult(search);
      });
  }

  function displayResult(query)
  {
      $.ajax({
          type : 'GET',
          url : '{{ url("search-campaign") }}',
          data : {'search' : query},
          dataType : 'html',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $("#display_campaign").html(result);
          },
          error : function(xhr, attr, throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
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
