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
                    <a href="{{route('broadcast_customer')}}" class="nav-link">See Broadcast Customer</a>
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
                <div class="card-header"><b>Broadcast</b></div>

                <div class="card-body">

                 <div class="mb-2 row">
                    <div class="col-md-3">
                        <a class="btn btn-success btn-sm" href="{{route('broadcastform')}}">Create Broadcast Reminder</a>
                    </div> 

                    <div class="col-md-2">
                        <a class="btn btn-success btn-sm" href="{{route('broadcasteventform')}}">Create Broadcast Event</a>
                    </div>
                 </div>

                <hr/>

                    <table class="table table-striped table-responsive" id="broadcast-list">
                        <thead>
                            <th>User</th>
                            <th>lists</th>
                            <th>Message</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->user_id}}</td>
                                    <td>{{$row->name}}</td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$row->id}}">{{$row->message}}</span>
                                        <div><small><a id="{{$row->id}}" class="display_popup">Read More</a></small></div>
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
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
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <p><!-- display message --></p>
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
        table();
    });

    function table(){
        $("#broadcast-list").dataTable({
            'pageLength':5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    }

    function getText(){
        $(".display_popup").click(function(){
            $("#myModal").modal();
            var id = $(this).attr('id');
            var txt = $(".get-text-"+id).text();
            $(".modal-body > p").text(txt);
        });
    }
</script>

@endsection
