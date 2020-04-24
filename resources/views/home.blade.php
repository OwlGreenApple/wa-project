@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>DASHBOARD</h2>
  </div>

  <div class="act-tel-dashboard-right">
    @if($status > 0)
      <div>Current plan : <b>{{ $membership }}</b></div>
      <div>Valid Until {{ $expired }}</div>
      <div>MESSAGES Quota {{ $quota }}</div>
      <div><a href="{{ url('pricing') }}"><i>Buy More</i></a></div>
    @endif
  </div>
  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="row justify-content-center act-tel-number">
    <div class="col-center col-lg-3 act-tel-number-left bg-dashboard">{{ $lists }} <br/><small>Lists</small></div>
    <div class="col-center col-lg-3 act-tel-number-right bg-dashboard">{{ $campaign }} <br/><small>Campaigns</small></div>
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
        <!-- sec column -->
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
        <!-- sec column -->
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
  $(document).ready(function(){
    checkPhone();
  });

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
</script>
@endsection
