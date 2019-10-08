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
                            <th>WA Number</th>
                            <th>Additional</th>
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
                                    <td> 
                                        @if($row->additional <> null)
                                        <a class="btn btn-info btn-sm addt" id="{{$row->id}}">Additional</a>
                                        @endif
                                    </td>
                                    
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

<!-- Modal -->
<div id="additional_popup" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Additonal Data</h4>
      </div>
      <div class="modal-body">
        <p id="displayadditional"></p>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        table();
        displayDataAdditional();
    });

    function displayDataAdditional(){
        $("body").on("click",".addt",function(){
            var listid = $(this).attr('id');
            var boxdata = '';
            $("#additional_popup").modal();

            $.ajax({
                type:'GET',
                url:'{{route("customeradditional")}}',
                data : {id:listid},
                dataType : 'json',
                success : function(response) 
                {
                    console.log(response.additonal);
                    $.each(response.additonal,function(key,value){

                        boxdata += '<div class="form-group row"><label class="col-md-3 col-form-label text-md-right"><b>'+key+'</b></label><div class="col-md-8">'+value+'</div></div></div>';
                    });
                    $("#displayadditional").html(boxdata);
                }
            });
        });
    }

     function table(){
        $("#user-customer").dataTable({
            'pageLength':10,
            'lengthMenu': [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    }
</script>
@endsection
