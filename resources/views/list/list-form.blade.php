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
                  <a href="{{route('userlist')}}" class="nav-link">Back To List</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container mb-2">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create List</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif 

                    @if (session('error_number'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error_number') }}
                        </div>
                    @endif 

                     <form name="event_form" method="POST" action="{{ route('addlist') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">WA Number</label>

                            <div class="col-md-8">
                               @if($data->count() > 0)
                                    <select name="wa_number" class="form-control">
                                        @foreach($data as $row)
                                            <option value="{{$row->wa_number}}">{{$row->wa_number}}
                                            </option>
                                        @endforeach
                                    </select>
                               @else
                                No Numbers
                               @endif


                                @if(session('wa_check_number'))
                                    <div class="error" role="alert">
                                        {{ session('wa_check_number') }}
                                    </div>
                                @endif 
                            </div>
                        </div>   

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">Category</label>

                            <div class="col-md-8">
                                <select name="category" class="form-control">
                                    <option value="0">Request Message</option>
                                    <option value="1">Event</option>
                                </select>

                                @if (session('category'))
                                    <div class="error">
                                        {{ session('category') }}
                                    </div> 
                                @endif
                                @if (session('isevent'))
                                    <div class="error">
                                        {{ session('isevent') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row dev">
                            <label class="col-md-3 col-form-label text-md-right">Event Date</label>

                            <div class="col-md-8">
                               <span id="event_date"></span>
                            </div>
                        </div>

                         @if (session('date_event'))
                         <div class="form-group row">
                             <label class="col-md-3 col-form-label text-md-right"></label>
                            <div class="col-md-8">
                               <div class="error">
                                    {{ session('date_event') }}
                                </div> 
                            </div>
                        </div>
                        @endif     

                        <!--<div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">Create input</label>

                            <div class="col-md-8">
                               <a id="open_modal" class="btn btn-primary btn-sm">Create Field</a>
                            </div>
                        </div>
                    -->

                         <div class="form-group">
                            <label>Page Header</label>
                            <div class="col-md-12">
                                 <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
                            </div>
                        </div> 
    
                                
                        <div class="form-group">
                            <label>Pixel</label>
                            <div class="col-md-12">
                                 <textarea name="pixel_txt" class="form-control"></textarea>
                            </div>
                        </div> 

                        <div class="form-group">
                            <label>Message</label>
                            <div class="col-md-12">
                                 <textarea name="message_txt" class="form-control"></textarea>
                            </div>
                        </div> 

                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add List
                                </button>
                            </div>
                        </div>
                     </form>
                     <!-- end form -->

                </div>
            </div>
        </div>
    </div>
<!-- end container -->   
</div>

<!-- Modal To Add Column -->
  <div class="modal fade" id="openMenu" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
            <h4>Create Field</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2"><input class="btn btn-default btn-sm add-field" type="button" value="Add Field" /></div>
                <form id="update_message">
                    <div class="form-group row">
                        <div id="append"></div>
                    </div>
                </form>
            </div>
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
        displayEventField();
        openMenu();
        addCols();
        delCols();
    });

    function openMenu()
    {
        $('#open_modal').click(function(){
            $("#openMenu").modal();
        });
    }

    /* Datetimepicker */
     $("body").on('focus','.evd',function () {
          $('#datetimepicker').datetimepicker({
            format : 'YYYY-MM-DD HH:mm',
          });
      });

     /* Display event date field */
    function displayEventField(){
        $(".dev").hide();
        $("select[name='category']").change(function(){
            var val = $(this).val();

            if(val == 1){
                $(".dev").show();
                $("#event_date").append("<input id='datetimepicker' type='text' name='date_event' class='form-control evd' />");
            } else {
                $(".dev").hide();
                $(".evd").remove();
            }
            
        });
    }

     function addCols(){
      $("body").on('click','.add-field',function(){
            var len = $(".fields").length;
             var box_html = '<input class="form-control col-sm-8 float-left fields pos-'+len+'" /><a id="'+len+'" class="del col-sm-4 btn btn-warning">Delete</a>';

        $("#append").append(box_html);
      });
    }

    function delCols(){
      $("body").on("click",".del",function(){
        var pos = $(this).attr('id');
        $(".pos-"+pos).remove();
        $("#"+pos).remove();
      });
    }

</script>
@endsection
