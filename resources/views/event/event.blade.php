@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('home')}}" class="nav-link">Back Home</a>
                </li>
                <li class="nav-item">
                  <a href="{{route('eventcustomer')}}" class="nav-link">See Event Subscribers</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><b>Event</b></div>

                 @if (session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                 @if (session('warning'))
                    <div class="alert alert-warning" role="alert">
                        {{ session('warning') }}
                    </div>
                 @endif

                <div class="card-body">
                     <div class="mb-2 row">
                        <div class="col-md-3">
                            <a class="btn btn-warning btn-sm" href="{{route('eventautoreply')}}">Create Event Auto Reply</a>
                        </div> 

                        <div class="col-md-2">
                            <a class="btn btn-warning btn-sm" href="{{route('eventform')}}">Create Event Schedule Message</a>
                        </div>
                     </div>

                      <hr/>

                     <!-- event auto reply -->
                    <div class="table-responsive">
                    <h4>Event Auto Reply</h4>
                    <hr/>
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>User</th>
                            <th>event</th>
                            <th>Days</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->user_id}}</td>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->days}}</td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$row->id}}">{{$row->message}}</span>
                                        <div><small><a id="{{$row->id}}" class="display_popup">Read More | Edit</a></small></div>
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        @php
                                            if($row->status == 1){
                                                $turn = 0;
                                            } else {
                                                $turn = 1;
                                            }
                                        @endphp
                                        <a href="{{url('eventautoreplyturn/'.$row->id.'/'.$turn.'')}}" class="btn btn-primary btn-sm"> @if($row->status == 0)
                                            Activate
                                        @else
                                            Deactivate
                                        @endif</a>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                'No Data'
                            @endif
                        </tbody>
                    </table>
                    </div>
                    <!-- end event autoreply -->

                    <hr/>

                    <!-- event schedule -->
                    <div class="table-responsive">
                    <h4>Event Schedule</h4>
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>User</th>
                            <th>event</th>
                            <th>Amount Days to Send</th>
                            <th>Sending Hour</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(!is_null($event))
                            @foreach($event as $rows)
                                <tr>
                                    <td>{{$rows->user_id}}</td>
                                    <td>{{$rows->name}}</td>
                                    <td>{{$rows->days}}</td>
                                    <td>{{$rows->hour_time}}</td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$rows->id}}">{{$rows->message}}</span>
                                        <div><small><a id="{{$rows->id}}" class="display_popup">Read More | Edit</a></small></div>
                                    </td>
                                    <td>{{$rows->created_at}}</td>
                                    <td>{{$rows->updated_at}}</td>
                                    <td>
                                        <a href="{{url('reminder-status/'.$rows->id.'/'.$rows->status.'')}}" class="btn btn-primary btn-sm"> @if($rows->status == 0)
                                            Activate
                                        @else
                                            Deactivate
                                        @endif</a>

                                        <div class="mt-1"><a class="btn btn-warning btn-sm edit-col" id="{{$rows->id}}">Edit</a></div>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                'No Data'
                            @endif
                        </tbody>
                    </table>
                    </div>
                    <!-- end event schedule -->

                </div>
                <!-- end card-body -->  
            </div>
        </div>
    </div>
<!-- end container -->   
</div>



 <!-- Modal -->
  <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            Event Message
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <form id="update_message">
                    <textarea id="divInput-description-post" rows="5" class="form-control message"></textarea><!-- display message -->
                    <input type="hidden" class="id_reminder" />
                    <div class="mt-2">
                        <button type="submit" class="btn btn-warning">Edit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="editbox" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            Event Message
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <form id="edit_event">
                   
                     <div class="form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Event Date</label>
                        <div class="col-md-6">
                            <input type="text" id="datetimepicker" class="form-control" name="date_event" autocomplete="off" />
                        </div>
                    </div>

                    <div class="form-group row">
                            <label class="col-md-4 col-form-label text-md-right">Choose Schedule</label>
                            <div class="col-md-6">
                                <select class="form-control" name="schedule" id="schedule">
                                  <option value="0">Hari H</option>
                                  <option value="1">H-</option>
                                  <option value="2">H+</option>
                                </select>
                            </div>
                    </div> 

                    <div class="form-group row thedayh">
                        <label class="col-md-4 col-form-label text-md-right">Days and time To send message</label>
                        <div class="col-md-6">
                            <input name="hour" id="hour" type="text" class="timepicker form-control" value="00:00" readonly />
                        </div>
                    </div> 

                    <div class="form-group row">
                         <div class="col-md-4 text-md-right"><a class="btn btn-success btn-sm add-day">Add Day and Time</a></div>
                        <div id="append" class="col-md-6"></div>
                    </div>

                    <div class="form-group mt-2">
                        <button type="submit" class="btn btn-warning">Edit</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      
    </div>
  </div>

<script type="text/javascript">
    $(document).ready(function(){
        getText();
        updateMessage();
        emojiOne();
        table();
        displayEditForm();
        displayAddDaysBtn();
        MDTimepicker();
        addDays();
        delDays();
        updateEventSchedule();
    });

    /* Datetimepicker */
     $(function () {
          $('#datetimepicker').datetimepicker({
            format : 'YYYY-MM-DD HH:mm',
          });
      });

      function MDTimepicker(){
        $("body").on('focus','.timepicker',function(){
            $(this).mdtimepicker({
              format: 'hh:mm',
            });
        });
      }

       function displayEditForm()
      {
        $("body").on('click','.edit-col',function(){
            var id = $(this).attr('id');
            $("#editbox").modal();
            $.ajax({
                type : 'GET',
                url : '{{route("displayeventschedule")}}',
                data : {'id':id},
                dataType : "json",
                success : function(result){
                   $("input[name='date_event']").val(result.date_event);
                   $("input[name='day']").val(result.day);
                   $("input[name='hour']").val(result.hour);
                }
            });
        });
      }

     function displayAddDaysBtn()
     {
        $(".add-day").hide();
        $("#schedule").change(function(){
          var val = $(this).val();

          if(val == 0){
            $(".thedayh").show();
            $("#hour").prop('disabled',false);
            $(".add-day").hide();
            $(".delcols").remove();
          } else {
            $(".thedayh").hide();
            $("#hour").prop('disabled',true);
            $(".add-day").show();
          }

        });
     }

    function addDays(){
      $("body").on('click','.add-day',function(){
        var day = $("#schedule").val();
        var pos = $(".days").length;
        
        if(day == 1){
             var box_html = '<select name="day[]" class="form-control col-sm-4 float-left days pos-'+pos+' delcols"><?php for($x=-90;$x<=-1;$x++) {
                echo "<option value=".$x.">$x</option>";
          }?></select>'+
          '<input name="hour[]" type="text" class="timepicker form-control float-left col-sm-4 pos-'+pos+' delcols" value="00:00" readonly />'+
          '<span><a id="pos-'+pos+'" class="btn btn-warning float-left del delcols">Delete</a></span>'+
          '<div class="clearfix"></div>';
        } else {
             var box_html = '<select name="day[]" class="form-control col-sm-4 float-left days pos-'+pos+' delcols"><?php for($x=1;$x<=100;$x++) {
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

    function updateEventSchedule()
    {
        $("body").on('submit','#edit_event',function(e){
            e.preventDefault();
            var data = $(this).serialize();
            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
            $.ajax({
                type : 'POST',
                url : "{{route('remindermessage')}}",
                data : data,
                dataType : "json",
                success : function(result){
                    alert(result.msg);
                    location.href="{{route('reminder')}}";
                }
            });/* end ajax */
        });
    }

    function table(){
        $("#user-list").dataTable({
            'pageLength':5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    }

    function getText(){
        $("body").on("click",".display_popup",function(){
            $("#myModal").modal();
            var id = $(this).attr('id');
            var txt = $(".get-text-"+id).text();
            $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(txt);
            $(".id_reminder").val(id);
        });
    }

    function emojiOne(){
        $("#divInput-description-post").emojioneArea({
            pickerPosition: "right",
            mainPathFolder : "{{url('')}}",
        });
    }

    /* Update message */
    function updateMessage(){
        $("body").on('submit','#update_message',function(e){
            e.preventDefault();
            var id_reminder = $('.id_reminder').val(); 
            var txt = $('.message').val();
            var data = {
                'message': txt,
                'id_reminder': id_reminder,
            };

            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
            $.ajax({
                type : 'POST',
                url : "{{route('remindermessage')}}",
                data : data,
                dataType : "json",
                success : function(result){
                    alert(result.msg);
                    location.href="{{route('reminder')}}";
                }
            });/* end ajax */
        });
    }
</script>

@endsection
