@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card">
                <div class="card-header"><b>User's List</b></div>

                <div class="card-body">
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>Product Name</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Status</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        @if($row->status == 1)
                                            <a class="btn btn-info btn-sm" href="{{$row->id}}">Active</a>
                                         @else
                                            <a class="btn btn-warning btn-sm" href="{{$row->id}}">Inactive</a>
                                        @endif    
                                    </td>
                                    <td><a class="btn btn-success btn-sm" href="{{url('/usercustomer/'.$row->id)}}">See Customers</a></td>
                                </tr>
                            @endforeach
                            @else
                                'No Data'
                            @endif
                        </tbody>
                    </table>
                    <a class="btn btn-default btn-sm" href="{{route('home')}}">Back To Add List</a>
                </div>
                <!-- end card-body -->  
            </div>
        </div>
    </div>
<!-- end container -->   
</div>
@endsection
