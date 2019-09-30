@extends('layouts.app')

@section('content')
<!-- navbar -->

<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('userlist')}}" class="nav-link">Back To List</a>
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
                <div class="card-header"><b>User's Customer</b></div>

                <div class="card-body">
                    <table class="table table-striped table-responsive" id="user-customer">
                        <thead>
                            <th>Customer's Name</th>
                            <th>Customer's WA</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <!--<th>Status</th>
                            <th>Action</th>-->
                        </thead>
                        <tbody>
                            @if($data->count() > 0)
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->wa_number}}</td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <!--<td>
                                        @if($row->status == 1)
                                            <a class="btn btn-info btn-sm" href="{{$row->id}}">Active</a>
                                         @else
                                            <a class="btn btn-warning btn-sm" href="{{$row->id}}">Inactive</a>
                                        @endif    
                                    </td>
                                    <td><a class="btn btn-success btn-sm" href="{{url('/usercustomer/'.$row->id)}}">Wait</a></td>
                                    -->
                                </tr>
                            @endforeach
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

<script type="text/javascript">
    $(document).ready(function(){
        table();
    });

     function table(){
        $("#user-customer").dataTable({
            'pageLength':10,
            //"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    }
</script>
@endsection
