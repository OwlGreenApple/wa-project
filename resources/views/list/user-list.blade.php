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
                     <a class="nav-link" href="{{route('home')}}">Back To Add List</a>
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
                <div class="card-header"><b>User's List</b></div>

                <div class="card-body">
                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>Product Name</th>
                            <th>Created</th>
                            <th>Updated</th>
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
                                        <a class="btn btn-success btn-sm" href="{{url('/usercustomer/'.$row->id)}}">See Customers</a>
                                        <a class="btn btn-info btn-sm edit" id="{{$row->id}}">Edit</a>
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
<div id="myModal" class="modal fade col-md-12" role="dialog">
  <div class="modal-dialog" style="max-width : 800px!important">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Name</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
         <form id="edit_list">
            <div class="form-group">
                <label class="col-form-label text-md-right"><b>Name</b></label>
                <input type="text" class="form-control" name="list_name"/>
                <span class="error list_name"></span>
            </div> 
            <div class="form-group">
               <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
            </div> 
            <input type="hidden" name="idlist"/>
            <button type="submit" class="btn btn-default">Edit List</button>
         </form>
      </div>
    </div>

  </div>
</div>

<script type="text/javascript">

  /* CKEditor */
    var editor = CKEDITOR.replace( 'editor1',{
        extraPlugins: ['filebrowser','colorbutton','justify','image2','font'],
        removePlugins : 'image',
    });
    CKFinder.setupCKEditor( editor );

    CKEDITOR.editorConfig = function( config ) {
        config.extraPlugins = 'filebrowser,colorbutton,justify,image2,font';
        config.removePlugins = 'image';
    };

    $(document).ready(function(){
        table();
        displayEditor();
        updateEditor();
    });

    function displayEditor(){
         $(".edit").click(function(){
            $("#myModal").modal();
            var id = $(this).attr('id');
            $("input[name='idlist']").val(id);
            $.ajax({
                type : 'GET',
                url : '{{route("displaylistcontent")}}',
                data : {'id':id},
                dataType : "json",
                success : function(result){
                   $("input[name='list_name']").val(result.name);
                   CKEDITOR.instances.editor1.setData( result.content );
                }
            });
        });
    }

    function updateEditor(){
        $("#edit_list").submit(function(e){
            e.preventDefault();
             var data = {
                id : $("input[name='idlist']").val(),
                name : $("input[name='list_name']").val(),
                editor : CKEDITOR.instances.editor1.getData()
             };

            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
             $.ajax({
                type : 'POST',
                url : '{{route("updatelistcontent")}}',
                data : data,
                dataType : "json",
                success : function(result){
                   alert(result.message);
                   location.href="{{route('userlist')}}";
                }
            });
        });
    }

     function table(){
        $("#user-list").dataTable({
            'pageLength':5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        });
    }
</script>
@endsection
