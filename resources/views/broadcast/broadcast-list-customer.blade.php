@extends('layouts.app')

@section('content')
<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('home')}}" class="nav-link">Back Home</a>
                </li>
                <li class="nav-item">
                     <a class="nav-link" href="{{route('broadcastlist')}}">Back Broadcast List</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Select User's List To Broadcast</b></div>

                <div class="card-body">
                    <form method="post" action="{{route('sendbroadcast')}}">
                     @csrf
                    <button type="submit" class="btn btn-warning">Broadcast!!!</button>
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>Product Name</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>
                                Check All<br/>
                                <input id="checkall" type="checkbox"/>
                            </th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>{{$row->name}}</td>
                                    <td>{{$row->created_at}}</td>
                                    <td>{{$row->updated_at}}</td>
                                    <td class="text-center"><input type="checkbox" name="{{$row->name}}" value="{{$row->id}}" class="form-check-input checks"/></td>
                                </tr>
                            @endforeach
                            @else
                                'No Data'
                            @endif
                        </tbody>
                    </table>
                    </form>
                </div>
                <!-- end card-body -->  
            </div>
        </div>
    </div>
<!-- end container -->   
</div>

<script type="text/javascript">
    $(document).ready(function(){
        checkAll();
    });

    function checkAll(){
        $("#checkall").click(function(){
            var is_checked = $(this).prop('checked');
            if(is_checked == true){
                $(".checks").prop('checked',true);
            } else {
                $(".checks").prop('checked',false);
            }
        });    
    }
</script>

@endsection
