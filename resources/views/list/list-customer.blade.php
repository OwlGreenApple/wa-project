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
                <div class="card-header"><b>List Subscriber</b></div>

                <div class="card-body">
                    <div><a href="{{url('export_csv_list_subscriber')}}/{{$listid}}" class="btn btn-success btn-sm">Export</a></div>
                    <!--<div><a id="{{$listid}}" class="btn btn-info btn-sm import-col">Import</a></div>-->

                    <table class="table table-striped table-responsive" id="user-customer">
                        <thead>
                            <th>Subscriber's Name</th>
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
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p id="displayadditional"></p>
      </div>
    </div>

  </div>
</div>

<!-- Import Modal -->
<div id="import_popup" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Import Data Subscribers</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="import_list_subscriber">
            <div class="form-group">
                <label class="col-form-label text-md-right"><b>File import(CSV)</b></label>
                <input type="file" class="form-control" name="csv_file" />
            </div>
            <input type="hidden" name="list_id_import"/>
            <button type="submit" id="btn-edit" class="btn btn-default">Save</button>
        </form>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        table();
        displayDataAdditional();
        openImport();
        csvImport();
    });

    //TO IMPORT
    function openImport()
    {
        $(".import-col").click(function(){
            $("#import_popup").modal();
            var id = $(this).attr('id');
            $("input[name='list_id_import']").val(id);
        });
    } 

    function csvImport()
    {
        $("body").on("submit","#import_list_subscriber",function(e){
            e.preventDefault();
            var form = $(this)[0];
            var data = new FormData(form);

            $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });
            $.ajax({
                type : 'POST',
                enctype: 'multipart/form-data',
                url : '{{url("import_csv_list_subscriber")}}',
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                data : data,
                dataType : 'json',
                success : function(response)
                {
                    alert(response.message);
                }
            })
        });
    }

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
                    //console.log(response.additonal);
                    $.each(response.additonal,function(key,value){

                        boxdata += '<div class="form-group row"><label class="col-md-3 col-form-label text-md-right"><b class="text-capitalize">'+key+'</b></label><div class="col-md-8 form-control">'+value+'</div></div></div>';
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
