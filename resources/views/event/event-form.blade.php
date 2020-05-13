@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>SETUP EVENT</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-campaign">
  <form id="save_campaign">
      <input type="hidden" name="campaign_id" value="new">
      <input type="hidden" name="reminder_id" value="new">
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Name :</label>
        <div class="col-sm-6">
          <input type="text" name="campaign_name" class="form-control" />
          <span class="error campaign_name"></span>
        </div>
      </div>

      <div class="form-group row lists">
        <label class="col-sm-3 col-form-label">Select List :</label>
        <div class="col-sm-9 relativity">
           <select name="list_id" class="custom-select-campaign form-control">
              @if(count($lists) > 0)
                @foreach($lists as $row)
                  <option value="{{ $row['id'] }}">{{ $row['customer_count'] }} {{ $row['label'] }}</option>
                @endforeach
              @endif
           </select>
           <span class="icon-carret-down-circle"></span>
           <span class="error list_id"></span>
        </div>
      </div>

      <div class="form-group row event-time">
        <label class="col-sm-3 col-form-label">Event Time :</label>
        <div class="col-sm-9 relativity">
          <input id="datetimepicker" type="text" name="event_time" class="form-control custom-select-campaign" />
          <span class="icon-calendar"></span>
          <span class="error event_time"></span>
        </div>
      </div>

      <div class="form-group row reminder">
        <label class="col-sm-3 col-form-label">Select Day :</label>
        <div class="col-sm-9 relativity">
           <select name="schedule" id="schedule" class="custom-select-campaign form-control">
              <option value="0">The Day</option>
              <option value="1">H-</option>
              <option value="2">H+</option>
           </select>
           <span class="icon-carret-down-circle"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Time to send Message :</label>
        <div class="col-sm-9 relativity">
          <span class="inputh">
            <input name="hour" type="text" class="timepicker form-control" value="00:00" readonly />
          </span>
          <!-- <span class="inputh">
             <select name="day" class="form-control col-sm-7 float-left days delcols mr-3"> @for($x=1;$x<=100;$x++) 
                  <option value="{{ $x }}">{{ $x }} days after event</option>;
             @endfor
             </select>
            <input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />
          </span> -->
          <span class="error day"></span><br/>
          <span class="error hour"></span>
          <small>Please set your timezone on : <a target="_blank" href="{{ url('settings/?mod=1') }}">Settings</a></small>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Image :</label>
        <div class="col-sm-9 relativity">
          <div class="custom-file">
            <input type="file" name="imageWA" class="custom-file-input pictureClass form-control" id="input-picture" accept="image/*">

            <label class="custom-file-label" for="inputGroupFile01">
            </label>
          </div>
          <span class="error image"></span><br/>
          <small>Maximum image size is : <b>4Mb</b></small>
          <div><small>Image Caption Limit is 1000 characters</small></div>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :
          <span class="tooltipstered" title="<div class='panel-heading'>Message</div><div class='panel-content'>
            You can use this as 'Personalization field' <br>
            [NAME] <br>
            [PHONE] <br>
            [WA] <br>
            Do NOT use : % or & character on your message<br>
            </div>">
            <i class="fa fa-question-circle "></i>
          </span>
        </label>
        <div class="col-sm-6">
          <textarea name="message" id="divInput-description-post" class="form-control"></textarea>
          <span class="error msg"></span>
        </div>
      </div>

      <div class="form-group row">

        <div class="text-right col-sm-9">
          <button type="submit" class="btn btn-custom">Create</button>
        </div>
      </div>
      
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Send 1 test Message
          <span class="tooltipstered" title="<div class='panel-heading'>Send 1 test Message</div><div class='panel-content'>
            Test Message will be send immediately
            </div>">
            <i class="fa fa-question-circle "></i>
          </span>
        </label>
        <div class="col-sm-9 relativity">
            <input type="text" id="phone" name="phone_number" class="form-control" />
            <span class="error code_country"></span>
            <span class="error phone_number"></span>
            <button type="button" class="btn btn-test">Send Test</button>
        </div>
      </div>
  </form>
</div>

<!-- give emoji -->
 <script type="text/javascript">
    $("#divInput-description-post").emojioneArea({
        pickerPosition: "right"
    });
</script>

<script type="text/javascript">

    /* Datetimepicker */
     $(function () {
          $('#datetimepicker').datetimepicker({
            format : 'YYYY-MM-DD HH:mm',
          });
      });

     $(function(){
        $('#tagname').on('click', function(){
            var tag = '{name}';
            var cursorPos = $('#divInput-description-post').prop('selectionStart');
            var v = $('#divInput-description-post').val();
            var textBefore = v.substring(0,  cursorPos );
            var textAfter  = v.substring( cursorPos, v.length );
            $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(textBefore+ tag +textAfter );
        });
     });

    $(document).ready(function(){
        displayTemplate();
        displayAddDaysBtn();
        MDTimepicker();
        neutralizeClock();
        //loader();
        //addDays();
        //delDays();
    });


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

     function displayAddDaysBtn()
     {
        $(".add-day").hide();
        $("#schedule").change(function(){
          var val = $(this).val();

          var hday = '<input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />';

          var hmin = '<select name="day" class="form-control col-sm-8 float-left days delcols"><?php for($x=-90;$x<=-1;$x++) {
                echo "<option value=".$x.">$x days before event</option>";
          }?></select>'+
          '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
          ;

          var hplus = '<select name="day" class="form-control col-sm-8 float-left days delcols"><?php for($x=1;$x<=100;$x++) {
                echo "<option value=".$x.">$x days after event</option>";
          }?></select>'+
          '<input name="hour" type="text" class="timepicker form-control col-sm-4 delcols" value="00:00" readonly />'
          ;

          if(val == 0){
           // $(".thedayh").show();
            //$("#hour").prop('disabled',false);
            //$(".add-day").hide();
            //$(".delcols").remove();
            $(".inputh").html(hday);
          } else if(val == 1) {
            //$(".thedayh").hide();
           // $("#hour").prop('disabled',true);
            //$(".add-day").show();
             $(".inputh").html(hmin);
          } else {
             $(".inputh").html(hplus);
          }

        });
     }


    function addDays(){
      $(".add-day").click(function(){
        var day = $("#schedule").val();
        var pos = $(".days").length;
        
        if(day == 1){
             var box_html = '<select name="day" class="form-control col-sm-4 float-left days pos-'+pos+' delcols"><?php for($x=-90;$x<=-1;$x++) {
                echo "<option value=".$x.">$x</option>";
          }?></select>'+
          '<input name="hour[]" type="text" class="timepicker form-control float-left col-sm-4 pos-'+pos+' delcols" value="00:00" readonly />'+
          '<span><a id="pos-'+pos+'" class="btn btn-warning float-left del delcols">Delete</a></span>'+
          '<div class="clearfix"></div>';
        } else {
             var box_html = '<select name="day" class="form-control col-sm-4 float-left days pos-'+pos+' delcols"><?php for($x=1;$x<=100;$x++) {
                echo "<option value=".$x.">$x</option>";
          }?></select>'+
            '<input name="hour[]" type="text" class="timepicker form-control float-left col-sm-4 pos-'+pos+' delcols" value="00:00" readonly />'+
            '<span><a id="pos-'+pos+'" class="btn btn-warning float-left del delcols">Delete</a></span>'+
            '<div class="clearfix"></div>';
        }

        $("#append").append(box_html);
      });
    }

    function delDays(){
      $("body").on("click",".del",function(){
        var pos = $(this).attr('id');
        $("."+pos).remove();
        $("#"+pos).remove();
      });
    }

</script>
@endsection
