@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2 class="campaign">Event List</h2>
  </div>

  <div class="act-tel-dashboard-right">
    <h5><a class="act-tel-apt-create" href="{{url('create-event')}}">SETUP EVENT</a></h5>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
    <div class="row">
      <div class="input-group col-lg-4">
          <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find event" >
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

<div class="container" id="display_list">
  @if(count($data) > 0)
    @include('event.event')
  @else
    <div class="bg-dashboard cardlist row">
      Sorry, the page you're currently page not available.
    </div>
  @endif
</div>

<!-- Modal Duplicate Event -->
  <div class="modal fade child-modal" id="modal_duplicate" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2 act-tel-campaign">
                  <form id="duplicate">
                    
                    <div class="form-group">
                      <label>Event Name</label>
                      <input type="text" class="form-control" name="campaign_name" />
                      <span class="error campaign_name"></span>
                    </div>

                    <div class="form-group">
                      <label>Event Date & Time</label>
                      <div class="relativity">
                        <input id="datetimepicker" type="text" name="event_time" class="form-control custom-select-campaign" />
                        <span class="icon-calendar"></span>
                      </div>
                      <span class="error event_time"></span>
                    </div>
                 
                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Save</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
               
            </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

<script type="text/javascript">

    /* $(function(){
        $('#tagname').on('click', function(){
            var tag = '{name}';
            var cursorPos = $('#divInput-description-post').prop('selectionStart');
            var v = $('#divInput-description-post').val();
            var textBefore = v.substring(0,  cursorPos );
            var textAfter  = v.substring( cursorPos, v.length );
            $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(textBefore+ tag +textAfter );
        });
     }); */

  $(document).ready(function(){
    // getText();
    emojiOne();
    pagination();
    MDTimepicker();
    duplicateEventForm();
    duplicateEvent();
    delEvent();
  });

  /* Datetimepicker */
  $(function () {
      $('#datetimepicker').datetimepicker({
        format : 'YYYY-MM-DD HH:mm',
        minDate : new Date()
      });
  });

  function MDTimepicker(){
    $("body").on('focus','.timepicker',function(){
        $(this).mdtimepicker({
          format: 'hh:mm',
        });
    });
  }

  function emojiOne(){
      $("#divInput-description-post").emojioneArea({
          pickerPosition: "right",
          mainPathFolder : "{{url('')}}",
      });
  }

   //ajax pagination
  function pagination()
  {
      $(".page-item").removeClass('active').removeAttr('aria-current');
      var mulr = window.location.href;
      getActiveButtonByUrl(mulr)
    
      $('body').on('click', '.pagination .page-link', function (e) {
          e.preventDefault();
          var url = $(this).attr('href');
          window.history.pushState("", "", url);
          loadPagination(url);
      });
  }

  function loadPagination(url) {
      $.ajax({
        beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
        url: url
      }).done(function (data) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          getActiveButtonByUrl(url);
          $('#display_list').html(data);
      }).fail(function (xhr,attr,throwable) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          alert("Sorry, Failed to load data! please contact administrator");
          console.log(xhr.responseText);
      });
  }

  function getActiveButtonByUrl(url)
  {
    var page = url.split('?');
    if(page[1] !== undefined)
    {
      var pagevalue = page[1].split('=');
      $(".page-link").each(function(){
         var text = $(this).text();
         if(text == pagevalue[1])
          {
            $(this).attr('href',url);
            $(this).addClass('on');
          } else {
            $(this).removeClass('on');
          }
      });
    }
    else {
        var mod_url = url+'?page=1';
        getActiveButtonByUrl(mod_url);
    }
  }

  //end ajax pagination

  function duplicateEventForm()
  {
    $("body").on("click",".event_duplicate",function(){
        var id = $(this).attr('id');
        $("#duplicate").attr('data',id);
        $("#modal_duplicate").modal();
    });
  }

  function duplicateEvent()
  {
    $("#duplicate").submit(function(e){
        e.preventDefault();
        var campaign_id = $(this).attr('data');
        var option_position = $("#campaign_option").val();

        var data = $(this).serializeArray();
        data.push({name : 'id', value:campaign_id});

        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type: 'POST',
          url: "{{ url('event-duplicate') }}",
          data: data,
          dataType: 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success: function(result) {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            if(result.success == 0)
            { 
              $(".error").show();
              $(".campaign_name").html(result.campaign_name);
              $(".event_time").html(result.event_time);
            }
            else
            {
              $(".error").hide();
              // alert(result.message);
              $("#modal_duplicate").modal('hide');
              $("#duplicate:input").val('');
              displayEvent();
            }
          },
          error : function(xhr,attr,throwable){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $(".error").hide();
            console.log(xhr.responseText);
          }
      });

    });
  }

  function delEvent()
  {
    $("body").on("click",".event-del",function(){
      var id = $(this).attr('id');
      var option_position = $("#campaign_option").val();
      var conf = confirm("Are you sure to delete this event?"+"\n"+"WARNING : This cannot be undone");

      if(conf == true)
      {
        $.ajax({
          type : 'GET',
          url : '{{ url("campaign-del") }}',
          data : {
            id : id,
            mode : "event"
          },
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            displayEvent();
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

  function displayEvent()
  {
      $.ajax({
          type : 'GET',
          url : '{{ url("display-event") }}',
          dataType : 'html',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(data)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            $('#display_list').html(data);
          },
          error : function(xhr, attr, throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
        });
  }

    /*function getText(){
        $("body").on("click",".display_popup",function(){
            $("#myModal").modal();
            var id = $(this).attr('id');
            var txt = $(".get-text-"+id).text();
            $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(txt);
            $(".id_reminder").val(id);
        });
    }*/
</script>

@endsection
