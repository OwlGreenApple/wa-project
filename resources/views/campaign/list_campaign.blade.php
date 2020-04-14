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
              @if($active == true)
                <!-- <th class="text-center">Edit</th> -->
                <th class="text-center">Delete</th>
              @else
                <th class="text-center">Status</th>
              @endif
            </tr>
          </thead>

          <tbody>
             
          </tbody>
        </table>
      </div>

    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="edit_appt" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Edit Campaign
        </h5>
      </div>

      <div class="modal-body col-lg-12">
        <form id="appt_form">
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Name</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 text-left row">
                <input name="customer_name" class="form-control" />
                <span class="error customer_name"></span>
              </div>
            </div> 

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Phone Number</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 text-left row">
                <input name="phone_number" class="form-control" />
                <span class="error phone_number"></span>
              </div>
            </div> 

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Choose Appointment Time :</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 relativity text-left row">
                <input autocomplete="off" id="datetimepicker" type="text" name="date_send" class="form-control custom-select-campaign" />
                <span class="icon-calendar"></span>
                <span class="error date_send"></span>
              </div>
            </div>

            <input type="hidden" name="campaign_id" />
            <span class="error campaign_id"></span>
            <span class="error db_error"><!-- internal error --></span>

            <div class="text-right">
              <button id="button" type="submit" class="btn btn-warning mr-1">Save</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
            </div>
        </form>

      </div>

    </div>   
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    $('#datetimepicker').datetimepicker({
        format : 'YYYY-MM-DD HH:mm',
        minDate : new Date()
    }); 
    // display_data();
    // searchData();
    openEditForm();
    // editContactAppointment();
    deleteCampaign();
    // tableData();
    tableAjax();
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
      }
    });
  }

  function display_data(query)
  {
    $.ajax({
      type : 'GET',
      url : 'test',
      data : {campaign_id : {!! $campaign_id !!}, search : query },
      dataType : 'html',
      beforeSend : function(){
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success : function(result)
      {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        $("#display_data").html(result);
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

  function searchData()
  {
     $(".search-box").keyup(delay(function(){
        var query = $(this).val();
        display_data(query);
     },1000));
  }

  function openEditForm()
  {
    $("body").on('click','.icon-edit',function(){
      var id = $(this).attr('id');
      var date = $(this).attr('data-ev');
      var customer = $(this).attr('data-name');
      var phone = $(this).attr('data-phone');
      var customer_id = $(this).attr('data-customer-id');

      $("input[name='customer_name']").val(customer);
      $("input[name='phone_number']").val(phone);
      $("input[name='date_send']").val(date);
      $("input[name='campaign_id']").val(id);
      $("#button").attr('csid',customer_id);
      $("#button").attr('oldtime',date);
      $(".error").hide();
      $("#edit_appt").modal();
    });
  }

  function editContactAppointment()
  {
      $("#appt_form").submit(function(e){
          e.preventDefault();
          var data = $(this).serializeArray();
          data.push(
            {name:'customer_id', value:$("#button").attr('csid')},
            {name:'oldtime', value:$("#button").attr('oldtime')},
          )
          saveEditForm(data);
      });
  }

  function saveEditForm(data)
  {
    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      type : 'POST',
      url : '{{ url("list-edit-apt") }}',
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

        if(result.success == 1)
        {
            // alert(result.message);
            $("#edit_appt").modal('hide');
            $(".error").hide();
            display_data();
        }
        else
        {
            $(".error").show();
            $(".customer_name").html(result.customer_name);
            $(".phone_number").html(result.phone_number);
            $(".date_send").html(result.date_send);
            $(".campaign_id").html(result.campaign_id);
            $(".db_error").html(result.customer_id);
            $(".db_error").html(result.oldtime);
            if(result.message !== undefined)
            {
                alert(result.message);
            }
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

  function deleteCampaign()
  {
    $("body").on("click",".icon-cancel",function(){
      var reminder_customer_id = $(this).attr('id');
      var data = {'reminder_customer_id' : reminder_customer_id}
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
        // alert(result.message);

        if(result.success == 1)
        {
            //display_data();
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