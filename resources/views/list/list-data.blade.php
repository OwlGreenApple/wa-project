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
          <input type="text" name="listname" class="form-control-lg col-lg-4 search-box" placeholder="Find a List By a name" >
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
    searchList();
    deleteList();
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

  function searchList(){
    $(".search-icon").click(function(){
        var listname = $("input[name=listname]").val();
        $.ajax({
          type : 'GET',
          url : '{{route("searchlist")}}',
          data : {'listname' : listname},
          dataType : 'html',
          success : function(result){
            $("#display_list").html(result);
          }
        });
    });
  }

   function deleteList(){
    $('body').on('click',".del",function(){
      var id = $(this).attr('id');
      var conf = confirm('Are you sure to delete this list?');

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : "{{route('deletelist')}}",
          data : {'id' : id},
          success : function(result){
            alert(result.message);
            displayData();
          }
        });
      } else {
        return false;
      }
     
    });
  }

</script>
@endsection

