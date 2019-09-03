@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-9">
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
        <div class="col-md-9">
            <div class="card">
                <div class="card-header"><b>Reminder</b></div>

                 @if (session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                 @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                 @endif

                <div><a class="btn btn-warning btn-sm" href="{{route('reminderform')}}">Create Reminder</a></div>

                <div class="card-body">
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>User</th>
                            <th>lists</th>
                            <th>Days</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Status</th>
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
                                        <div><small><a id="{{$row->id}}" class="display_popup">Read More</a></small></div>
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        @if($row->status == 0)
                                            Disabled
                                        @else
                                            Active
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{url('reminder-status/'.$row->id.'/'.$row->status.'')}}" class="btn btn-primary btn-sm">Set Status</a>
                                    </td>
                                </tr>
                            @endforeach
                            @else
                                'No Data'
                            @endif
                        </tbody>
                    </table>
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
                    <button type="submit" class="btn btn-warning">Edit</button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

<script type="text/javascript">
    $(document).ready(function(){
        getText();
        updateMessage();
        emojiOne();
    });

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
