@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2 class="campaign">List Appointment Reminder</h2>
  </div>

  <div class="act-tel-dashboard-right">
    <a href="{{url('create-appointment')}}" class="btn btn-custom">Create Appointment</a>
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
        
        
      <div class="bg-dashboard campaign row">
          <div class="col-lg-4 pad-fix col-card">
            <h5>Dr Visit
                <span>
                  <a data-link="{{env('APP_URL')}}xyz" class="btn-copy icon-copy"></a>
                </span>  
            </h5>                           
            <div class="notes">
              <div class="link_wrap">Link From : {{env('APP_URL')}}xyz
                <span>
                  <a data-link="{{env('APP_URL')}}xyz" class="btn-copy icon-copy"></a>
                </span>
              </div>
              
              <div>List : Test List Audience</div>
            </div>
            <div class="created">
              Create On : 10 march 2020
            </div>
          </div>

          <div class="col-lg-5 pad-fix mt-4">
            <div class="row">
                <div class="col-lg-3 pad-fix cardnumber">
                &nbsp
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <a href="{{ url('list-apt') }}" target="_blank">
                    <div class="big-number">52</div>
                    <div class="contact">Contact</div>
                  </a>
                </div>  
                <!--<div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">7</div>
                  <div class="contact">Send</div>
                </div> 
                -->
            </div>  
          </div>

          <div class="col-lg-3 pad-fix col-button">
              <a href="{{url('edit-apt')}}" class="btn btn-edit btn-sm" target="_blank"><span class="icon-edit"></span></a>
              <a href="" class="btn btn-success btn-sm" target="_blank"><span class="icon-export"></span></a>
              <button type="button" id="2" class="btn btn-danger btn-sm event-del"><span class="icon-delete"></span></button>

          </div>
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
  });

//belum jadi
  function displayAppointment()
  {
     $.ajax({
        type : 'GET',
        url : '{{ route("eventlist") }}',
        data : {type : 0},
        dataType : 'html',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#display_campaign").html(result);
        },
        error : function(xhr,attributes,throwable){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        }
     });
  }

//belum jadi 
  function delAppointment()
  {
    $("body").on("click",".broadcast-del",function(){
      var id = $(this).attr('id');
      var conf = confirm("Are you sure to delete this broadcast?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("campaign-del") }}',
          data : {
              id : id,
              mode : "broadcast"
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
