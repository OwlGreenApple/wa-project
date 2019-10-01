@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('home')}}" class="nav-link">Back Home</a>
                </li>
                <li class="nav-item">
                  <a href="{{route('reminder_customer')}}" class="nav-link">See Reminder Customers</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header"><b>Reminder</b></div>

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
                    <h3>Reminder Auto Reply</h3>

                    <hr/>

                     <div class="mb-2 row">
                        <div class="col-md-3">
                            <a class="btn btn-info btn-sm" href="{{route('reminderautoreply')}}">Create Reminder Auto Reply</a>
                        </div> 

                        <div class="col-md-2">
                            <a class="btn btn-info btn-sm" href="{{route('reminderform')}}">Create Reminder Schedule Message</a>
                        </div>
                     </div>

                    <hr/>

                    <div class="table-responsive">
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>User</th>
                            <th>lists</th>
                            <th>Days</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if($autoreply->count() > 0)
                            @foreach($autoreply as $row)
                                <tr>
                                    <td>{{$row->user_id}}</td>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->days}}</td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$row->id}}">{{$row->message}}</span>
                                        <div><small><a id="{{$row->id}}" class="display_popup">Read More</a></small></div>
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        <a href="{{url('reminder-status/'.$row->id.'/'.$row->status.'')}}" class="btn btn-primary btn-sm"> @if($row->status == 0)
                                            Run
                                        @else
                                            Pause
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
                </div>
                <!-- end card-body -->  


                 <div class="card-body">
                    <h3>Reminder Message</h3>

                    <hr/>

                    <div class="table-responsive">
                    <table class="table table-striped table-responsive" id="reminder-message">
                        <thead>
                            <th>User</th>
                            <th>lists</th>
                            <th>Days</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if($data->count() > 0)
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->user_id}}</td>
                                    <td>{{$row->name}}</td>
                                    <td> 
                                        <span class="get-day-{{$row->id}}">{{$row->days}}
                                        </span>
                                        <!--<div class="mt-1"><small><a class="display_days" id="{{$row->id}}">Edit</a></small></div>-->
                                    </td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$row->id}}">{{$row->message}}</span>
                                        <div><small><a id="{{$row->id}}" class="display_popup">Read More</a></small></div>
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        <a href="{{url('reminder-status/'.$row->id.'/'.$row->status.'')}}" class="btn btn-primary btn-sm"> @if($row->status == 0)
                                            Run
                                        @else
                                            Pause
                                        @endif</a>

                                          <a id="{{$row->id}}" class="btn btn-danger btn-sm del-col">Delete</a>
                                         <a id="{{encrypt($row->list_id)}}" class="btn btn-info btn-sm download-col">Download CSV</a>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                    </div>
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
            Reminder Message
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

  <!-- Modal Edit Days -->
  <div class="modal fade" id="editDays" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            Reminder Message
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <form id="update_days">
                    <select class="form-control" name="day">
                      @php
                      for($x=1;$x<=100;$x++){
                       @endphp
                        <option value="{{$x}}">+{{$x}}</option>
                      @php  
                      }
                      @endphp
                    </select>
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

<script type="text/javascript">
    $(document).ready(function(){
        getText();
        updateMessage();
        emojiOne();
        table();
        getDays();
        updateDays();
        delDays();
        csvReminder();
    });

    function table(){
        $("#user-list").dataTable({
            'pageLength':5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });

        $("#reminder-message").dataTable({
            'pageLength':5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    }

    function getDays(){
        $("body").on("click",".display_days",function(){
            $("#editDays").modal();
            var id = $(this).attr('id');
            var day = $(".get-day-"+id).text();
            day = parseInt(day);
            $(".id_reminder").val(id);
            $('select[name="day"] > option[value="' +day+ '"]').prop('selected',true);
        });
    }

    /* Update message */
    function updateDays(){
        $("body").on('submit','#update_days',function(e){
            e.preventDefault();
            var id_reminder = $('.id_reminder').val(); 
            var days = $('select[name="day"]').val();
            var data = {
                'days': days,
                'id_reminder': id_reminder,
            };

            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
            $.ajax({
                type : 'POST',
                url : "{{route('reminderdays')}}",
                data : data,
                dataType : "json",
                success : function(result){
                    if(result.msg.length > 0){
                        alert(result.msg);
                        location.href="{{route('reminder')}}";
                    }
                }
            });/* end ajax */
        });
    }

    function delDays(){
        $("body").on('click','.del-col',function(){
            var id = $(this).attr('id');
            var conf = confirm('Are you sure?');

            if(conf == true){
                 $.ajax({
                    type : 'GET',
                    url : '{{route("delreminder")}}',
                    data : {'id':id},
                    dataType : "json",
                    success : function(result){
                      alert(result.message);
                      location.reload(true);
                    }
                });
            } else {
                return false;
            }
        });
    }

    function csvReminder(){
         $("body").on('click','.download-col',function(){
            var id = $(this).attr('id');
             $.ajax({
                type : 'GET',
                url : '{{route("export_reminder_subscriber")}}',
                data : {'id':id},
                dataType : "json",
                success : function(result){
                   location.href=result.url;
                }
            });
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
