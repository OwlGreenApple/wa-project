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

<!-- Modal Copy Link -->
<div class="modal fade" id="copy-link" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Copy Link
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        You have copied the link!
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          OK
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  $(document).ready(function(){
    displayData();
    searchList();
    deleteList();
    duplicateList();
    copyLink();
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
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            alert(result.message);
            displayData();
          }
        });
      } else {
        return false;
      }
     
    });
  }

  function duplicateList(){
    $("body").on("click",".duplicate",function(){
        var id = $(this).attr('id');
        var conf = confirm('Are you want to duplicate this list?');
        
        if(conf == true)
        {
          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
           $.ajax({
              type : 'POST',
              url : '{{route("duplicatelist")}}',
              data : {'id' : id},
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

                 if(result.error == true)
                 {
                    alert(result.message);
                 }
                 else
                 {
                    alert(result.message);
                    displayData();
                 }
              }
          });
        }
        else {
          return false;
        }
        
    });
    }

    function copyLink(){
      $( "body" ).on("click",".btn-copy",function(e) 
      {
        e.preventDefault();
        e.stopPropagation();

        var link = $(this).attr("data-link");

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $('#copy-link').modal('show');
      });
    }

</script>
@endsection

