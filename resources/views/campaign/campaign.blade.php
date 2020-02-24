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
      <div id="search-event" class="col-lg-12">
        <!-- display search event -->
      </div>
      <div id="search-responder" class="col-lg-12">
        <!-- display search auto responder -->
      </div>
      <div id="search-broadcast" class="col-lg-12">
        <!-- display search campaign -->
      </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
      displayCampaign();
      displayEvent();
      delBroadcast();
      delAutoResponder();
      delEvent();
      searchCampaign();
  });

  function displayCampaign() {
      $("#campaign_option").change(function(){
          var val = $(this).val();

          if(val == 0){
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

  function displayEvent(search)
  {
     $.ajax({
        type : 'GET',
        url : '{{ route("eventlist") }}',
        data : {search : search},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          if(search == null){
            $("#search-event").html('');
            $("#search-responder").html('');
            $("#search-broadcast").html('');
            $("#display_campaign").html(result);
          }
          else {
            $("#display_campaign").html('');
            $("#search-event").html(result);
          }
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        }
     });
  }

  function displayBroadcast(search)
  {
    $.ajax({
        type : 'GET',
        url : '{{ route("broadcastlist") }}',
        data : {search : search},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          
          if(search == null){
            $("#search-event").html('');
            $("#search-responder").html('');
            $("#search-broadcast").html('');
            $("#display_campaign").html(result);
          }
          else {
            $("#display_campaign").html('');
            $("#search-broadcast").html(result);
          }
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert(xhr.responseText());
        }
    });
  }

  function displayAutoResponder(search)
  {
    $.ajax({
        type : 'GET',
        url : '{{ route("reminderlist") }}',
        data : {search : search},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          
          if(search == null){
            $("#search-event").html('');
            $("#search-responder").html('');
            $("#search-broadcast").html('');
            $("#display_campaign").html(result);
          }
          else {
            $("#display_campaign").html('');
            $("#search-responder").html(result);
          }
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
      var conf = confirm("Are you sure to delete this broadcast?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("broadcast-del") }}',
          data : {id : id},
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            alert(result.message);
            displayBroadcast();
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
      var conf = confirm("Are you sure to delete this auto responder?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("reminder-del") }}',
          data : {id : id},
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            alert(result.message);
            displayAutoResponder();
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
      var conf = confirm("Are you sure to delete this event?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("event-del") }}',
          data : {id : id},
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            alert(result.message);
            displayEvent();
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
        displayBroadcast(search);
        displayAutoResponder(search);
        displayEvent(search);
      });
  }

</script>
@endsection
