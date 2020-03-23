@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>LIST Appointment User : </h2>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container act-tel-tab">
  <div class="input-group col-lg-4">
      <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find a campaign by a name" >
      <span class="input-group-append">
        <div class="btn search-icon">
            <span class="icon-search"></span>
        </div>
      </span>
  </div> 
</div>

<div class="container act-tel-apt">
    <table class="table table-bordered mt-4">
      <thead class="bg-dashboard">
        <tr>
          <th colspan="1" class="text-center">No Order</th>
          <th class="text-center">Date Appointment</th>
          <th class="text-center">Name Contact</th>
          <th class="text-center">WA Contact</th>
          <th colspan="2" class="text-center">Action</th>
        </tr>
      </thead>

      <tbody id="display_data"></tbody>
    </table>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    display_data();
  });

  function display_data()
  {
    $.ajax({
      type : 'GET',
      url : '{{ url("list-table-apt") }}',
      data : {campaign_id : {!! $campaign_id !!} },
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
</script>
@endsection