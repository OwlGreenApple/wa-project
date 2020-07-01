@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>DASHBOARD</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="row justify-content-center act-tel-number">
    <div class="col-center col-lg-3 act-tel-number-left bg-dashboard"><a href="{{ url('lists') }}">{{ $lists }} <br/><small>Lists</small></a></div>
    <div class="col-center col-lg-3 act-tel-number-right bg-dashboard"><a href="{{ url('campaign') }}">{{ $campaign }} <br/><small>Campaigns</small></a></div>
    <div class="col-center col-lg-3 account_status">
       @if($status > 0)
        <div>Current plan : <b>{{ $membership }}</b></div>
        <div>Valid Until {{ $expired }}</div>
        <div>MESSAGES Quota {{ $quota }}</div>
        <div>Phone Status : {!! $phone_status !!}</div>
        <div>Server Status : {!! $server_status !!}</div>
        <div><a href="{{ url('pricing') }}"><span>Buy More</span></a></div>
        <div>
          <button class="btn btn-lg <?php if ($user->is_started) { echo "btn-danger"; } else { echo "btn-success"; } ?>" id="button-run">
          <?php if ($user->is_started) { ?>
            <i class="fa fa-stop"></i> Stop
          <?php } else { ?>
            <i class="fa fa-chevron-circle-right"></i> Start
          <?php } ?>
          </button>
          <select class="form-control" id="select-speed">
            <option value="0" <?php if ($user->speed == 0) { echo "selected"; } ?>>Slow</option>
            <option value="1" <?php if ($user->speed == 1) { echo "selected"; } ?>>Normal</option>
            <option value="2" <?php if ($user->speed == 2) { echo "selected"; } ?>>Fast</option>
          </select>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- LIST -->
<div class="container act-tel-dashboard">
  <div class="act-tel-list-board">
    <div class="act-tel-list-left">List</div>
    <div class="act-tel-list-right"><span class="icon-carret-down-circle"></span></div>
    <div class="clearfix"></div>
  </div>

  <div class="act-tel-list bg-dashboard row col-fix">
   
      <div class="col-md-5 act-tel-list-left-col">
        <div class="big-number">{{ $latest_lists }}</div>
        <div class="contact">New Contact Lists (7 days)</div>

        <div class="row mt-3">
          <div class="col-lg-5 col-number"><b>{{ $lists }}</b> <div class="ml-1 act-tel-list-m-list">List</div></div>
          <div class="col-lg-7 col-number"><b>{{ $contact }}</b> <div class="ml-1 act-tel-list-m-list">Total Contacts</div></div>
        </div>

        <div class="mt-2">
          <a href="{{ url('lists') }}" target="_blank" class="btn btn-custom">View List</a>
        </div>
      </div>

      <div class="col-md-7 act-tel-bg-list">
        <!-- new contact list per day -->
        <div id="user-charts" style="height: 300px; width: 100%;"></div>
      </div>
    </div>

</div>

<!-- CAMPAIGNS -->
<div class="container act-tel-dashboard">
  <div class="act-tel-list-board">
    <div class="act-tel-list-left">Campaigns</div>
    <div class="act-tel-list-right"><span class="icon-carret-down-circle"></span></div>
    <div class="clearfix"></div>
  </div>

  <div class="act-tel-dashboard view-campaign bg-dashboard row col-fix">
   
      <div class="col-md-5 act-tel-list-left-col">

        <div class="row mt-3">
          <div class="col-lg-6 col-number"><b>{{ $total_message }}</b> <div class="ml-1 act-tel-list-m-list">Total Message</div></div>
          <div class="col-lg-6 col-number"><b>{{ $total_sending_message }}</b> <div class="ml-1 act-tel-list-m-list">Total Send</div></div>
        </div>
        <!--
        <div class="row mt-3">
          <div class="col-lg-6 col-number"><b>1000</b> <div class="ml-1 act-tel-list-m-list">Total Opened</div></div>
          <div class="col-lg-6 col-number"><b>100%</b> <div class="ml-1 act-tel-list-m-list">Total Open Rate</div></div>
        </div>
        -->
        <div class="mt-2">
          <a href="{{ url('campaign') }}" target="_blank" class="btn btn-custom">View Campaigns</a>
        </div>
      </div>

      <div class="col-md-7 act-tel-bg-list">
        <!-- Total message sent -->
        <div id="message-charts" style="height: 200px; width: 100%;"></div>
      </div>
    </div>

</div>

<!-- Modal Import Contact -->
  <div class="modal fade child-modal" id="leadsettings" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title text-center">
            <span id="auth_message"></span>
          </h5>
        </div>

        <div class="modal-body text-center">
            <a href="{{url('pricing')}}" class="btn btn-primary btn-lg">Buy Now</a>
        </div>

      </div>
      
    </div>
  </div>
  <!-- End Modal -->

<script type="text/javascript">
  window.onload = function () 
  {
    /** TOTAL CONTACTS ADDING PER DAY **/
    var contacts = [];
    $.each(<?php echo json_encode($graph_contacts);?>, function( i, item ) {
        contacts.push({'x': new Date(i), 'y': item});
    });

    var chart = new CanvasJS.Chart("user-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Total registered users in 30 days",
        fontFamily: "Nunito,sans-serif",
        fontSize : 18
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total registered users",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints: contacts,
        color : "#2cb06a"
      }]
    });
    chart.render();
    //{x : new Date('2019-12-04'), y: 520, indexLabel: "highest",markerColor: "red", markerType: "triangle" },

    /** TOTAL SENT MESSAGE PER DAY **/

    var sends = [];
    $.each(<?php echo json_encode($graph_messages);?>, function( i, item ) {
        sends.push({'x': new Date(i), 'y': item});
    });

    var chart_message = new CanvasJS.Chart("message-charts", {
      animationEnabled: true,
      theme: "light2",
      title:{
        text: "Total Message Sent in 30 Days",
        fontFamily: "Nunito,sans-serif"
      },
      axisY: {
          titleFontFamily: "Nunito,sans-serif",
          titleFontSize : 14,
          title : "Total Message sent",
          titleFontColor: "#b7b7b7",
          includeZero: false
      },
      data: [{        
        type: "line",       
        dataPoints: sends,
        color : "#f99f1b"
      }]
    });
    chart_message.render();
		$(".canvasjs-chart-credit").hide();
    //{x : new Date('2019-12-04'), y: 520, indexLabel: "highest",markerColor: "red", markerType: "triangle" },
  }


  function checkPhone() {
      $.ajax({
        type : 'GET',
        url : '{{url("checkphone")}}',
        success : function(result){

          if(result.status == 'phone'){
            $("#leadsettings").modal({
              show: true,
              keyboard: false,
              backdrop: 'static'
            });
            $("#auth_message").html('Please connect your phone :');
            $(".btn-primary").html('Settings')
            $(".btn-primary").attr('href','{{ url("settings") }}')
          }
          else if(result.status == 'buy')
          {
            $("#leadsettings").modal({
              show: true,
              keyboard: false,
              backdrop: 'static'
            }); 
            $("#auth_message").html('Please make order here :');
          }
          else if(result.status == 'exp')
          {
            $("#leadsettings").modal({
              show: true,
              keyboard: false,
              backdrop: 'static'
            }); 
            $("#auth_message").html('Your membership has expired please buy more to continue');
          }
        }
      });
  }
	
  function stopStart() {
    $("body").on("click","#button-run",function(){
      $.ajax({
        type : 'GET',
        url : '{{url("stop-start")}}',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          if(result.status == 'success'){
            if(result.isStarted){
              $("#button-run").removeClass("btn-success");
              $("#button-run").addClass("btn-danger");
              $("#button-run").html('<i class="fa fa-stop"></i> Stop');
            }
            else {
              $("#button-run").removeClass("btn-danger");
              $("#button-run").addClass("btn-success");
              $("#button-run").html('<i class="fa fa-chevron-circle-right"></i> Start');
            }
          }
        }
      });
    });
  }
  
  function change_speed() {
    $( "#select-speed" ).change(function() {
      $.ajax({
        type : 'GET',
        url : '{{url("change-speed")}}',
        data : {
          speed : $( this ).val()
        },
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          if(result.status == 'success'){
            alert("Speed changed");
          }
        }
      });
    });
  }
  
  $(document).ready(function(){
    checkPhone();
    stopStart();
    change_speed();
		$(".canvasjs-chart-credit").hide();
  });

</script>
@endsection
