@extends('layouts.app')

@section('content')
<!-- navbar -->

<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-11">
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
        <div class="col-md-11">
            <div class="card">
                <div class="card-header"><b>User's List</b></div>

                <div class="card-body">
                     <a class="btn btn-primary btn-sm mb-3" href="{{route('createlist')}}">Create Lists</a>

                    <table class="table table-striped table-responsive" id="user-list">
                        <thead>
                            <th>List Name</th>
                            <th>Category</th>
                            <th>Subcribers</th>
                            <th>Date Event</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                          @if($data !== null)
                            @foreach($data as $row => $val)
                                <tr>
                                    <td>
                                        @if($val[0]->is_event == 0)
                                            <a target="_blank" href="{{url('/'.$val[0]->name)}}" class="copy">{{$val[0]->name}}</a>
                                        @else
                                           <a target="_blank" href="{{url('ev/'.$val[0]->name)}}" class="copy">{{$val[0]->name}}</a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($val[0]->is_event == 0)
                                            Message
                                        @else
                                            Event
                                        @endif
                                    </td>
                                    <td>{{$val[1]}}</td>
                                    <td>{{$val[0]->event_date}}</td>
                                    <td>{{$val[0]->created_at}}</td>
                                    <td>{{$val[0]->updated_at}}</td>
                                    <td>
                                        <a class="btn btn-success btn-sm" href="{{url('/usercustomer/'.$val[0]->id)}}">See Subscribers</a>
                                        <a class="btn btn-info btn-sm edit" id="{{$val[0]->id}}">Edit</a> 
                                        <a class="btn btn-danger btn-sm del" id="{{$val[0]->id}}">Delete</a>
                                    </td>
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
<div id="myModal" class="modal fade col-md-12" role="dialog">
  <div class="modal-dialog" style="max-width : 800px!important">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit : <span class="list_name"></span></h4>
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
               <label><b>Page Header</b></label>
               <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
            </div> 

            <div class="form-group">
               <label><b>Pixel</b></label>
               <textarea name="pixel_txt" class="form-control"></textarea>
            </div>  

            <div class="form-group">
               <label><b>Message</b></label>
               <textarea name="message_txt" class="form-control"></textarea>
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
    CKEDITOR.replace( 'editor1',{
        filebrowserBrowseUrl: "{{ route('ckbrowse') }}",
        filebrowserUploadUrl: "{{ route('ckupload') }}",
        extraPlugins: ['uploadimage','colorbutton','justify','image2','font'],
        removePlugins : 'image',
    });

    CKEDITOR.editorConfig = function( config ) {
        config.extraPlugins = 'uploadimage','colorbutton','justify','image2','font';
        config.removePlugins = 'image';
    };

    $(document).ready(function(){
        table();
        displayEditor();
        updateEditor();
        delEditor();
        //copyClipBoard();
        //bootstrapTooltip();
    });

     function table(){
         $("#user-list").dataTable({
            'pageLength':10,
            //"lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            "destroy":true,
        });
    }

    /* clipboard */
    function copyClipBoard()
    {
         var clipboard = new ClipboardJS('.copy');

        /*clipboard.on('success', function(e) {
            //alert('You had copied the text');
            clipboard.destroy();
        });*/
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
                   $(".list_name").html(result.list_name);
                   $("textarea[name='pixel_txt']").val(result.pixel);
                   $("textarea[name='message_txt']").val(result.message);
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
                editor : CKEDITOR.instances.editor1.getData(),
                pixel : $("textarea[name='pixel_txt']").val(),
                message : $("textarea[name='message_txt']").val(),
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

    function delEditor(){
      $("body").on("click",".del",function(){
        var q = confirm('Are you sure to delete?');
        var id = $(this).attr('id');

        if(q == true){
           $.ajax({
              type : 'GET',
              url : '{{route("deletelistcontent")}}',
              data : {'id':id},
              dataType : "json",
              success : function(result){
                 alert(result.message);
                 location.reload(true);
              }
           });
        } else {
          return false;
        }

      });
    }

</script>
@endsection
