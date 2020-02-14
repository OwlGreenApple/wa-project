@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>LISTS</h2>
  </div>

  <div class="act-tel-dashboard-right">
     <a href="{{url('createlists')}}" class="btn btn-custom">Create List</a>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
      <div class="input-group">
          <input type="text" class="form-control-lg col-lg-4 search-box" placeholder="Find a List By a name" >
          <span class="input-group-append">
            <div class="btn search-icon">
                <span class="icon-search"></span>
            </div>
          </span>
      </div>
  </div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="act-tel-tab">
      <div class="col-lg-12" id="display_list">
        <!-- display data -->
      </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    displayData();
  });

  function displayData(){
    $.ajax({
      type : 'GET',
      url : '{{url("lists-table")}}',
      dataType : 'html',
      success : function(result){
        $("#display_list").html(result);
      }
    });
  }
</script>
@endsection

