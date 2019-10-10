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
                            <th>List URL</th>
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
                                    <td>{{$val[0]->label}}</td>
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
                <label><b>Create input additional</b></label>
                <div class="col-md-6 row">
                 <select id="type_fields" class="form-control col-md-8">
                      <option value="1">Fields</option>
                      <option value="2">Dropdown</option>
                  </select>
                  <input class="btn btn-default btn-sm add-field col-md-4" type="button" value="Add Field" />
                 </div> 
            </div>


            <div class="form-group">
               <label><div class="error errfield"></div></label>
                  <span id="additional"></span>
                  <button id="cid" type="button" class="btn btn-primary btn-sm">Save</button>
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

<!-- Modal Dropdown -->
  <div class="modal fade child-modal" id="openDropdown" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2">
                    <input class="btn btn-default btn-sm add-option" type="button" value="Add Option" />
                </div>
                 <div class="form-group">
                    <label>Dropdown name</label>
                   <input id="dropdown_name" type="text" class="form-control" />
                </div> 
                <label>Option Value</label>
                <div id="appendoption" class="form-group row">
                   <!-- display input here -->
                </div> 
                <div class="form-group">
                   <button id="cdp" class="btn btn-success btn-sm">Create Dropdown</button>
                </div>
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
        table();
        fixModal();
        displayEditor();
        updateEditor();
        delEditor();
        delCols();
        addCols();
        saveFields();
        displayDropdownMenu();
        addDropdownToField();
        addDropdown();
    });

    function table(){
         $("#user-list").dataTable({
            'pageLength':10,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
        });
    }

     /* Datetimepicker */
     $("body").on('focus','.evd',function () {
          $('#datetimepicker').datetimepicker({
            format : 'YYYY-MM-DD HH:mm',
          });
      });

     /* Fix bootstrap modal if stuck after open another modal */
     function fixModal()
     {
      $(document).find('.child-modal').on('hidden.bs.modal', function () {
          //console.log('hiding child modal');
          $('body').addClass('modal-open');
      });
     }

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

                   var box_html = '';
                   var is_option = {};
                   var options = '';

                   $.each(result.additional,function(key, value){
                      var len = key;
                      // dropdown
                      if(value.is_field == 1 && value.id_parent == 0)
                      {
                       box_html += '<div class="col-md-9 row dropdown"><input id='+value.id+' pos="'+value.id+'" class="fields pos-'+len+' form-control col-sm-6 toggledropdown" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a></div>';
                      }

                      // option
                      if(value.is_field == 0 && value.id_parent > 0)
                      {
                        box_html += '<div class="col-md-9 row hiddendropdown togglepos-'+value.id_parent+'"><input id='+value.id+' pos="'+len+'" class="fields pos-'+len+' form-control col-sm-6 float-left dropdownopt" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a></div><div class="clearfix"></div>';
                      }

                      if(value.is_field == 0 && value.id_parent == 0)
                      {
                         box_html += '<div class="col-md-3 col-form-label text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input id='+value.id+' name="fields[]" class="form-control mb-2 col-md-6 fields pos-'+len+'" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-md-2 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><select class="pos-'+len+' sel_is_option form-control col-md-3 selopt-'+len+'"><option value="0">Optional</option><option value="1">Require</option></select></div>';
                           is_option[len] = value.is_optional;
                      }  
                     
                   });

                   $("#additional").html(box_html);
                   $.each(is_option,function(key, value){
                      $(".selopt-"+key+"").val(value);
                   });
                   CKEDITOR.instances.editor1.setData( result.content );
                   var clen = $(".fields").length;
                    if(clen == 0)
                    {
                      $("#cid").hide();
                    } else {
                      $("#cid").show();
                    }
                }
            });
        });
    }

    function addCols(){
      $("body").on('click','.add-field',function(){
        var type = $("#type_fields").val();
        var len = $(".fields").length;
        var box_html;

         box_html = '<div class="col-md-3 col-form-label text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input name="fields[]" class="form-control mb-2 col-md-6 fields pos-'+len+'" /><a id="'+len+'" class="del_fields mb-2 col-md-2 btn btn-warning">Delete</a><select name="isoption[]" class="pos-'+len+' form-control col-md-3"><option value="0">Optional</option><option value="1">Require</option></select></div>';
       
        if(len < 5 && type == 1)
        {
            $("#additional").append(box_html);
        } 
        else if(len < 5 && type == 2) {
            $("#openDropdown").modal();
        }
        else 
        {
            alert('You only can create 5 inputs');
        }

      });
    } 

    function addDropdown()
    {
        $("body").on("click",".add-option",function(){
            var flen = $(".fields").length;
            var len = $(".doption").length;
            var dropdown = '<input class="form-control mb-2 col-sm-8 float-left fields doption pos-'+len+'" /><a id="pos-'+len+'" class="deloption mb-2 col-sm-3 btn btn-warning">Delete</a>';

            if(flen < 5){
                $("#appendoption").append(dropdown);
            } else {
                alert('You only can create 5 inputs');
            }
        });
    }

    function addDropdownToField()
    {
         $("body").on("click","#cdp",function(){
            $("#cid").show();
            var len = $(".fields").length;
            var options = '';
            var optionName = $("#dropdown_name").val();
            $(".doption").each(function(){
                value = $(this).val();
                options += '<input class="dropfield-'+len+' form-control dropdownopt pos-'+len+'" value="'+value+'"/>';
            });
            var box_html = '<div class="col-md-9 row"><input name="dropdown[]" pos="'+len+'" class="fields pos-'+len+' form-control col-sm-6 toggledropdown" value="'+optionName+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning pos-'+len+'">Delete</a><div style="padding : 0" class="pos-'+len+' col-sm-9 togglepos-'+len+' hiddendropdown mb-2">'+options+'</div></div>';
            
            $(".doption, .deloption").remove();

            if(len < 5)
            {
                $("#additional").append(box_html);
            }
            else 
            {
                alert('You only can create 5 inputs');
            }

         });
    }

    function saveFields(){
        $("#cid").click(function(){
            var data = {};
            var len = $(".fields").length;
            var dlen;
            var dropfields;

            /* Fields */
            for(x=0;x<len;x++)
            {
               var opt = [];
               var fields = $(".fields").eq(x).val();
               var idfields = $(".fields").eq(x).attr('id');
               var is_option = $(".sel_is_option").eq(x).val();
               var list_id = $("input[name='idlist']").val();


               var posfields = $(".fields").eq(x).attr('pos');
               dlen = $(".dropfield-"+posfields).length;


              /*dropfield */
                for(d=0;d<dlen;d++)
                {
                   dropfields = $(".dropfield-"+posfields).eq(d).val();
                   opt[d] = dropfields;
                }
                 
               data[x] = {
                    id : idfields,
                    field : fields, 
                    is_option : is_option, 
                    listid : list_id, 
                    dropfields : opt, 
               };
               
            }
            
            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
             $.ajax({
                type : 'POST',
                url : '{{route("updateadditional")}}',
                data : data,
                dataType : "json",
                success : function(result){
                  if(result.error == false)
                  {
                       if(result.listid.length > 0)
                        {
                           displayAjaxCols(result.listid);
                        }
                  }
                 
                  if(result.error == true)
                  {
                    $(".errfield").html('<div class="alert alert-danger">'+result.err+'</div>');
                  } else {
                    $(".errfield").html('');
                    alert(result.msg);
                  }
                }
            });
        });
    }

    function displayAjaxCols(id)
    {
      var box_html = '';
      var is_option = {};
      $.ajax({
        type : 'GET',
        url : '{{route("displayajaxfield")}}',
        data : {'id':id},
        dataType : "json",
        success : function(result){
            if(result.additional !== null)
            {
                $.each(result.additional,function(key, value){
                var len = key;
               // dropdown
                      if(value.is_field == 1 && value.id_parent == 0)
                      {
                       box_html += '<div class="col-md-9 row dropdown"><input id='+value.id+' pos="'+value.id+'" class="fields pos-'+len+' form-control col-sm-6 toggledropdown" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a></div>';
                      }

                      // option
                      if(value.is_field == 0 && value.id_parent > 0)
                      {
                        box_html += '<div class="col-md-9 row hiddendropdown togglepos-'+value.id_parent+'"><input id='+value.id+' pos="'+len+'" class="fields pos-'+len+' form-control col-sm-6 float-left dropdownopt" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a></div><div class="clearfix"></div>';
                      }

                      if(value.is_field == 0 && value.id_parent == 0)
                      {
                         box_html += '<div class="col-md-3 col-form-label text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input id='+value.id+' name="fields[]" class="form-control mb-2 col-md-6 fields pos-'+len+'" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-md-2 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><select class="pos-'+len+' sel_is_option form-control col-md-3 selopt-'+len+'"><option value="0">Optional</option><option value="1">Require</option></select></div>';
                           is_option[len] = value.is_optional;
                      }  

                 });

                 $("#additional").html(box_html);
                 //to make is optionnal choosen according on DB
                 $.each(is_option,function(key, value){
                      $(".selopt-"+key+"").val(value);
                 });
            }
        }
      });  
     
    }

    function displayDropdownMenu()
    {
        $("body").on("click",".toggledropdown",function(){
            var id = $(this).attr('pos');
            $(".togglepos-"+id).slideToggle();
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

    function delCols(){
      $("body").on("click",".del_fields",function(){
        var len = $(".fields").length;
        var pos = $(this).attr('id');
        var id_attribute = $(this).attr('idbase');
        var listid = $(this).attr('listid');
        var conf = confirm('Are you sure want to delete this fields?');

        if(conf == true)
        {
            if(id_attribute == undefined && listid == undefined)
            {
                $(".pos-"+pos).remove();
            } else {
              $.ajax({
                type : 'GET',
                url : '{{route("delfield")}}',
                data : {'id':id_attribute, 'list_id':listid},
                success : function(response){
                  alert(response.msg);
                  $(".pos-"+pos).remove();
                }
              });
            }
        } else {
          return false;
        }

        if(len < 2)
        {
            $("#cid").hide();
        }

      });
    }  

</script>
@endsection
