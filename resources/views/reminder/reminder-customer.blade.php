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
                     <a class="nav-link" href="{{route('reminder')}}">Back To Reminder List</a>
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
                <div class="card-header"><b>Reminder Customer's List</b></div>

                <div class="card-body table-responsive">
                    <table class="table table-striped" id="user-list">
                        <thead>
                            <th>Product Name</th>
                            <th>Reminder ID</th>
                            <th>Customer WA Number</th>
                            <th>Message</th>
                            <th>Customer Registered</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Send Status</th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->reminder_id}}</td>
                                    <td>{{$row->wa_number}}</td>
                                    <td class="wraptext">
                                        <span class="get-text-{{$row->id}}">{{$row->message}}</span>
                                    </td>
                                    <td>{{$row->csrg}}</td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td>
                                        @if($row->status == 0)
                                            Message not deliver
                                        @else
                                            Message has delivered already    
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
@endsection
