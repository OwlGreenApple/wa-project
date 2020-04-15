@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>List Users</h2>
    <h4>Campaign Name : {{ $campaign_name }}</h4>
  </div>
  <div class="clearfix"></div>
</div>
<!-- 
<div class="container act-tel-tab">
  <div class="input-group col-lg-4">
      <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find customer by name" >
      <span class="input-group-append">
        <div class="btn search-icon">
            <span class="icon-search"></span>
        </div>
      </span>
  </div> 
</div> -->

<div class="container act-tel-apt">
    <div class="col-lg-12">
      <div class="row col-lg-5">

        <div class="col-lg-3 pad-fix"><a href="{{ url('list-campaign') }}/{!! $campaign_id !!}/{{ $is_event }}/1" @if($active == true)class="act-tel-apt-create" @endif>QUEUE</a></div>

        <div class="col-lg-3 pad-fix"><a href="{{ url('list-campaign') }}/{!! $campaign_id !!}/{{ $is_event }}/0" @if($active == false)class="act-tel-apt-create" @endif>DELIVERED</a></div>

      </div>

      <div class="mt-4">
        @if($is_event == 'broadcast')
          <div id="broadcast_table"></div>
        @else
          <table id="list_campaign" class="display w-100">
            <thead class="bg-dashboard">
              <tr>
                <th class="text-center">No</th>
                @if($is_event == 1)
                <th class="text-center">Date Event</th>
                <th class="text-center">H</th>
                @endif
                @if($is_event == 0)
                <th class="text-center">H+</th>
                @endif
                <th class="text-center">Name Contact</th>
                <th class="text-center">WA Contact</th>
                @if($active == 1)
                  <!-- <th class="text-center">Edit</th> -->
                  <th class="text-center">Delete</th>
                @else
                  <th class="text-center">Status</th>
                @endif
              </tr>
            </thead>

             @if($campaigns->count() > 0)
            <tbody>
               @include('campaign.list_table_campaign')
            </tbody>
             @endif
          </table>
        @endif
      </div>
     

    </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#datetimepicker').datetimepicker({
        format : 'YYYY-MM-DD HH:mm',
        minDate : new Date()
    }); 
    display_broadcast_data();
    deleteCampaign();
    tableData();
  });

  function tableData()
  {
    $("#list_campaign").DataTable({
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
    });
  }

  function tableAjax()
  {
    $("#list_campaign").DataTable({
      "destroy":true,
      "lengthMenu": [ 10, 25, 50, 75, 100, 250, 500 ],
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url" : "{{ url('list-datatable-campaign') }}",
        "data": {
            "active": "{{ $active }}", 
            "campaign_id": "{!! $campaign_id !!}",
            "is_event": "{{ $is_event }}",
        }
      },
      'columnDefs': [
          { className: "text-center", targets: "_all" },
      ],
    });
  }

  function display_broadcast_data()
  {
    $.ajax({
      type : 'GET',
      url : '{{ url("list-broadcast-campaign") }}',
      data : {campaign_id : {!! $campaign_id !!}, active : {!! $active !!} },
      dataType : 'html',
      beforeSend : function(){
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success : function(result)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        $("#broadcast_table").html(result);
      },
      error : function(xhr)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        console.log(xhr.responseText);
      }
    });
  }

  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  function deleteCampaign()
  {
    $("body").on("click",".icon-cancel",function(){
      var is_broadcast = $(this).attr('data-broadcast');
      var data;

      if(is_broadcast == 1)
      {
        var broadcast_customer_id = $(this).attr('id');
        data = {'broadcast_customer_id' : broadcast_customer_id, 'is_broadcast' : 1};
      }
      else
      {
        var reminder_customer_id = $(this).attr('id');
        data = {'reminder_customer_id' : reminder_customer_id};
      }
     
      var warning = confirm('Are you sure to cancel this user?'+'\n'+'WARNING : This cannot be undone');

      if(warning == true)
      {
          exDeleteCampaign(data);
      }
      else
      {
          return false;
      }

    });
  }

  function exDeleteCampaign(data)
  {
    $.ajax({
      type : 'GET',
      url : '{{ url("list-delete-campaign") }}',
      data : data,
      dataType : 'json',
      beforeSend : function(){
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success : function(result)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        var table = $("#list_campaign").DataTable();

        if(result.success == 1)
        {
            if(result.broadcast == 1)
            {
              display_broadcast_data();
            }
            else
            {
              table.destroy();
              tableAjax();
            }
        }
        else
        {
            alert('Unable to cancel your campaign, sorry our server is too busy.');
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

</script>
@endsection