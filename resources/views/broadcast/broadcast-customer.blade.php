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
                     <a class="nav-link" href="{{route('broadcast')}}">Back Broadcast List</a>
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
                <div class="card-header"><b>Broadcast Customer's List</b></div>

                <div class="card-body table-responsive">
                    <table class="table table-striped" id="broadcast-customer">
                        <thead>
                            <th>Product Name</th>
                            <th>Broadcast ID</th>
                            <th>Customer WA Number</th>
                            <th>Message</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Send Status</th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->broadcast_id}}</td>
                                    <td>{{$row->wa_number}}</td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$row->id}}">
                                            {{$row->message}}
                                        </span>
                                        <div><small>
                                            <a id="{{$row->id}}" class="display_popup">Read More</a>
                                        </small></div>
                                    </td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        @if($row->status == 0)
                                            Pending
                                        @elseif($row->status == 1)
                                            Queued  
                                        @elseif($row->status == 2)
                                            Sent 
                                        @elseif($row->status == 5)
                                            Failed    
                                        @endif
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
        $("#broadcast-customer").dataTable({
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
