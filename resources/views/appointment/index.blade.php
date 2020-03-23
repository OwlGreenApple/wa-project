@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2 class="campaign">List Appointment Reminder</h2>
  </div>

  <div class="act-tel-dashboard-right">
    <a href="{{url('create-apt')}}" class="btn btn-custom">Create Appointment</a>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
    <div class="row">
      <div class="input-group col-lg-4">
          <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find a list by name" >
          <span class="input-group-append">
            <div class="btn search-icon">
                <span class="icon-search"></span>
            </div>
          </span>
      </div> 

      <div class="col-lg-6"></div>

      <div class="clearfix"></div>

    </div>
  </div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="act-tel-tab">
      <div id="display_appointment" class="col-lg-12">
        <!-- display appointment -->
        
        
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
      displayAppointment();
      displayResult();
      delAppointment();
      searchAppointment();
      MDTimepicker(); 
      neutralizeClock();
      copyLink();
  });

  function displayAppointment()
  {
     $.ajax({
        type : 'GET',
        url : '{{ url("table-apt") }}',
        data : {search : 0},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#display_appointment").html(result);
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
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

  function delAppointment()
  {
    $("body").on("click",".campaign-del",function(){
      var id = $(this).attr('id');
      var conf = confirm("Are you sure to delete this broadcast?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("campaign-del") }}',
          data : {
              id : id,
          },
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


  function searchAppointment()
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
