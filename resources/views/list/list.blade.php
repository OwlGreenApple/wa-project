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
                <div class="card-header"><b>User's List</b></div>

                <div class="card-body">
                     <a class="btn btn-primary btn-sm mb-3" href="{{route('createlist')}}">Create Lists</a>

                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>List Name</th>
                            <th>Category</th>
                            <th>Date Event</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            @if(!is_null($data))
                            @foreach($data as $row)
                                <tr>
                                    <td>
                                        @if($row->is_event == 0)
                                            <a data-toggle="tooltip" data-clipboard-text="{{url('/'.$row->name)}}" class="copy tip">{{$row->name}}</a>
                                        @else
                                           <a data-toggle="tooltip" data-clipboard-text="{{url('ev/'.$row->name)}}" class="copy tip">{{$row->name}}</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->is_event == 0)
                                            Message
                                        @else
                                            Event
                                        @endif
                                    </td>
                                    <td>{{$row->event_date}}</td>
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
            <div class="form-group dtev">
                <label class="col-form-label text-md-right"><b>Event Date</b></label>
                <span id="event_date"></span>
                <span class="error event_date"></span>
            </div>

            <div class="form-group">
               <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
            </div> 
            <input type="hidden" name="idlist"/>
            <input type="hidden" name="page_position"/>
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
        copyClipBoard();
        bootstrapTooltip();
    });

     function table(){
         $("#user-list").dataTable({
            'pageLength':5,
            "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "destroy":true,
        });
    }

    /* clipboard */
    function copyClipBoard()
    {
         var clipboard = new ClipboardJS('.copy');

        clipboard.on('success', function(e) {
            //alert('You had copied the text');
            clipboard.destroy();
        });
    }

    /* Tool Tip */
    function bootstrapTooltip()
    {
        $("body").on("click",".tip",function(){
             $('[data-toggle="tooltip"]').tooltip({
                animated: 'fade',
                placement: 'top',
                title : 'Click To Copy Link',
             });
        });
    }

     /* Datetimepicker */
     $("body").on('focus','.evd',function () {
          $('#datetimepicker').datetimepicker({
            format : 'YYYY-MM-DD HH:mm',
          });
      });

    function displayEditor(){
         $("body").on('click','.edit',function(){
            $("#myModal").modal();
            var databutton = $(".paginate_button.current").attr("data-dt-idx"); // get page button
            var id = $(this).attr('id');
            $("input[name='idlist']").val(id);
            var classLength = $(".evd").length;

            $.ajax({
                type : 'GET',
                url : '{{route("displaylistcontent")}}',
                data : {'id':id},
                dataType : "json",
                success : function(result){
                    if(result.is_event == 0){
                        $(".dtev").hide();
                        $(".evd").remove();
                    } else {
                        $(".dtev").show();
                        if(classLength == 0){
                            $("#event_date").append("<input id='datetimepicker' type='text' name='date_event' class='form-control evd' />");
                        }
                        $("input[name='date_event']").val(result.event_date);
                        $("input[name='page_position']").val(databutton);
                    }
                   CKEDITOR.instances.editor1.setData( result.content );
                }
            });
        });
    }

    function updateEditor(){
        $("#edit_list").submit(function(e){
            e.preventDefault();
             var databutton = $("input[name='page_position']").val(); // get data button position
             databutton = parseInt(databutton) -1;

             var data = {
                id : $("input[name='idlist']").val(),
                date_event : $("input[name='date_event']").val(),
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
                   table();
                   /*$("#user-list").dataTable({
                        "pageLength":5,
                        "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                        "destroy":true,
                     }).destroy();
                   table.page(databutton).draw( 'page' );*/
                   //location.href="{{route('userlist')}}";
                }
            });
        });
    }

</script>
@endsection
