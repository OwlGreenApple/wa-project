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
                                        <a class="btn btn-warning btn-sm duplicate" id="{{$val[0]->id}}">Duplicate</a>
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
        <h4 class="modal-title">
            <div>URL : <span class="list_name"></span></div>
        </h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <div class="modal-body">
         <form id="edit_list">
            <div class="form-group">
                <label class="col-form-label text-md-right"><b>List Name</b></label>
                <input type="text" class="form-control list_label" name="list_label" />
                <span class="error list_label"></span>
            </div> 

            <div class="form-group">
                <label class="col-form-label text-md-right"><b>Bot API</b></label>
                <input type="text" class="form-control" name="bot_api" />
                <span class="error bot_api"></span>
            </div>

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
            <button type="submit" id="btn-edit" class="btn btn-default">Save</button>
         </form>
      </div>
    </div>

  </div>
</div>

 <!-- Modal Add Fields -->
  <div class="modal fade child-modal" id="openFields" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2">
                    <input class="btn btn-info btn-sm add-field-column" type="button" value="Add Fields" />
                </div>
               
                <label>Field List</label>
                <form id="addFieldsForm">
                    <span id="append_fields"></span>
                    <input type="hidden" name="field_list"/>
                    <div class="form-group">
                       <button id="cfd" class="btn btn-success btn-sm">Create Fields</button>
                    </div>
                </form>
            </div>
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
                 <form id="dropdownForms">

                     <div class="form-group">
                        <label>Dropdown name</label>
                       <input name="dropdowname" type="text" class="form-control" />
                     </div> 
                     <label>Option Value</label>
                      <div id="appendoption" class="form-group row">
                         <!-- display input here -->
                      </div> 
                      <input type="hidden" name="dropdownlist"/>
                      <div class="form-group">
                         <button id="cdp" class="btn btn-success btn-sm">Create Dropdown</button>
                      </div>

                 </form>
            </div>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Modal Edit Dropdown -->
  <div class="modal fade child-modal" id="editDropdown" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2">
                    <input class="btn btn-warning btn-sm add-edit-option" type="button" value="Add Option" />
                </div>
               
                <label>Option List</label>
                <form id="optionform">
                    <div id="editoptions" class="form-group row">
                       <!-- display input here -->
                    </div> 

                    <input type="hidden" name="parent_id"/>
                    <input type="hidden" name="list_id"/>
                    <div class="form-group">
                       <button id="edp" class="btn btn-success btn-sm">Edit Dropdown</button>
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
        allowedContent: true,
        filebrowserBrowseUrl: "{{ route('ckbrowse') }}",
        filebrowserUploadUrl: "{{ route('ckupload') }}",
        extraPlugins: ['uploadimage','colorbutton','justify','image2','font','videoembed'],
        removePlugins : 'image',
    });

    CKEDITOR.editorConfig = function( config ) {
        config.extraAllowedContent = true;
        config.extraPlugins = 'uploadimage','colorbutton','justify','image2','font','videoembed';
        config.removePlugins = 'image';
    };


    var limit = 'You only can create 5 fields';

    $(document).ready(function(){
        table();
        fixModal();
        displayEditor();
        updateEditor();
        delEditor();
        delCols();
        addCols();
        addFields();
        //displayDropdownMenu();
        insertDropdown();
        addDropdown();
        editOption();
        addOption();
        insertOption();
        delOption();
        insertFields();
        openAdditional();
        duplicateList();
    });

    function duplicateList(){
        $("body").on("click",".duplicate",function(){
            var id = $(this).attr('id');
            $("#div-loading").show();
            $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
            });
             $.ajax({
                type : 'POST',
                url : '{{route("duplicatelist")}}',
                data : {'id' : id},
                dataType : "json",
                success : function(result)
                {
                   $("#div-loading").hide();

                   if(result.error == true)
                   {
                      alert(result.message);
                   }
                   else
                   {
                      alert(result.message);
                      location.href = '{{route("userlist")}}';
                   }
                }
            });

        });
    }

    function openAdditional()
    {
       $("#cid").click(function(){
          var listid = $("input[name='idlist']").val();
          $("input[name='listidaddt']").val(listid);
          $("#editFields").modal();
       });
    }

    function table(){
         $("#user-list").dataTable({
            'pageLength':10,
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
                   $(".list_label").val(result.list_label);
                   $(".list_name").html(result.list_name);
                   $("textarea[name='pixel_txt']").val(result.pixel);
                   $("textarea[name='message_txt']").val(result.message);
                   $("input[name='bot_api']").val(result.bot_api);

                   var box_html = '';
                   var is_option = {};
                   var options = '';

                   $.each(result.additional,function(key, value){
                      var len = key;
                      // dropdown
                      if(value.is_field == 1 && value.id_parent == 0)
                      {
                       box_html += '<div class="col-md-9 row dropdown pos-'+len+'"><input field="1" id='+value.id+' pos="'+value.id+'" class="cidlen colfields dropfields form-control col-sm-6" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><a class="btn btn-info col-sm-2 mb-2 btn-sm edit-option" id="'+value.id+'" list_id = '+value.list_id+'>Edit Option</a></div>';
                      }

                      /* option
                      if(value.is_field == 0 && value.id_parent > 0)
                      {
                        box_html += '<div class="col-md-9 row hiddendropdown togglepos-'+value.id_parent+'"><div class="dropfield pos-'+len+' form-control col-sm-6 float-left">'+value.name+'</div><div class="clearfix"></div></div>';
                      }
                      */

                      if(value.is_field == 0 && value.id_parent == 0)
                      {
                         box_html += '<div class="col-md-3 text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input field="0" id='+value.id+' name="field[]" class="cidlen form-control mb-2 col-md-6 fields colfields pos-'+len+'" value="'+value.name+'" /><a id="'+len+'" class="del_fields pos-'+len+' mb-2 col-md-2 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><select name="is_option[]" class="is_option pos-'+len+' form-control col-md-3 selopt-'+len+'"><option value="0">Optional</option><option value="1">Require</option></select></div>';
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

    /* RESTORE HTML AFTER UPDATE OR DELETE */
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
                      // DROPDOWN
                      if(value.is_field == 1 && value.id_parent == 0)
                      {
                        box_html += '<div class="col-md-9 row dropdown pos-'+len+'"><input field="1" id='+value.id+' pos="'+value.id+'" class="cidlen colfields dropfields form-control col-sm-6" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><a class="btn btn-info col-sm-2 mb-2 btn-sm edit-option" id="'+value.id+'" list_id = '+value.list_id+'>Edit Option</a></div>';
                      }

                      /*option
                      if(value.is_field == 0 && value.id_parent > 0)
                      {
                        box_html += '<div class="col-md-9 row hiddendropdown togglepos-'+value.id_parent+'"><input id='+value.id+' pos="'+len+'" class="fields pos-'+len+' form-control col-sm-6 float-left dropdownopt" value="'+value.name+'" /><a id="'+len+'" class="del_fields pos-'+len+' mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a></div><div class="clearfix"></div>';
                      }*/

                      if(value.is_field == 0 && value.id_parent == 0)
                      {
                           box_html += '<div class="col-md-3 text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input field="0" id='+value.id+' name="field[]" class="cidlen form-control mb-2 col-md-6 fields colfields pos-'+len+'" value="'+value.name+'" /><a id="'+len+'" class="del_fields pos-'+len+' mb-2 col-md-2 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><select name="is_option[]" class="is_option pos-'+len+' form-control col-md-3 selopt-'+len+'"><option value="0">Optional</option><option value="1">Require</option></select></div>';
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

    /* EDIT OR UPDATE LIST */
    function updateEditor(){
        $("#edit_list").submit(function(e){
            e.preventDefault();
             var databutton = $("input[name='page_position']").val(); // get data button position
             databutton = parseInt(databutton) -1;
             $("#div-loading").show();

             var fields = $(".fields");
             var isoption = $(".is_option");
             var dropfields = $(".dropfields");
             var datafields = {};
             var datadropfields = {};

             //fields
             for(i=0;i<fields.length;i++)
             {  
                var values = fields.eq(i).val();
                var idfields = fields.eq(i).attr('id');
                var fieldoption = isoption.eq(i).val();
                datafields[i] = {field:values, idfield : idfields, isoption : fieldoption};
             }

             //dropfields
             for(j=0;j<dropfields.length;j++)
             {  
                var dropvalues = dropfields.eq(j).val();
                var dropid = dropfields.eq(j).attr('id');
                datadropfields[j] = {field:dropvalues, idfield : dropid};
             }

             // all data
             var data = {
                id : $("input[name='idlist']").val(),
                list_label : $("input[name='list_label']").val(),
                bot_api : $("input[name='bot_api']").val(),
                date_event : $("input[name='date_event']").val(),
                editor : CKEDITOR.instances.editor1.getData(),
                pixel : $("textarea[name='pixel_txt']").val(),
                message : $("textarea[name='message_txt']").val(),
                fields : datafields,
                dropfields : datadropfields,
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
                   $("#div-loading").hide();
                   if(result.error == undefined)
                   {
                      $(".list_label, .event_date").html('');
                      alert(result.message);
                      displayAjaxCols(result.listid);
                   }
                   else if(result.additionalerror == true)
                   {
                      alert(result.message);
                   }
                   else
                   {
                      $(".list_label").html(result.label);
                      $(".event_date").html(result.date_event);
                      $(".bot_api").html(result.botapi);
                   }
                }
            });

        });
    }

    /* EDIT DROPDOWN OPTIONS */
    function editOption()
    {
      $("body").on("click",".edit-option",function(){
         var id = $(this).attr('id');
         var listid =  $("input[name='idlist']").val();
         var box_html = '';

         $("#editDropdown").modal();
         $("input[name='parent_id']").val(id);
         $("input[name='list_id']").val(listid);

         $.ajax({
            type : 'GET',
            url : '{{route("editdropfields")}}',
            data : {'id':id},
            dataType : 'json',
            success : function(result)
            {
               $.each(result.dropfields,function(key, value){
                  var len = key;
                  box_html += '<input id='+value.id+' class="dropdownopt form-control mb-2 col-sm-8 float-left doption opt-'+len+'" value="'+value.name+'" /><a id="opt-'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning deloption" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a>';
                });
                $("#editoptions").html(box_html);
            }
         });

      });
    }

    /* ADD DROPDOWN OPTIONS */
    function addOption()
    {
        $("body").on("click",".add-edit-option",function(){
            var len = $(".doption").length;
            var dropdownOptions = '<input class="newoption form-control mb-2 col-sm-8 float-left doption opt-'+len+'" /><a id="opt-'+len+'" class="deloption mb-2 col-sm-3 btn btn-warning">Delete</a>';

            $("#editoptions").append(dropdownOptions);
        });
    }

    /* SAVE DROPDOWN OPTIONS TO DATABASE */
    function insertOption()
    {
        $("body").on("submit","#optionform",function(e){
          e.preventDefault();
          var dataedit = {};
          var dlen = $(".dropdownopt");
          var values = [];
          var id = [];
          var parent_id = $("input[name='parent_id']").val();
          var list_id = $("input[name='list_id']").val();
          var newopt = $(".newoption");
          var data = [];
  
          for(i=0;i<dlen.length;i++)
          {
            values[i] = dlen.eq(i).val();
            id[i] = dlen.eq(i).attr('id');
          }

          for(j=0;j<newopt.length;j++)
          {
            data[j] = newopt.eq(j).val();
          }

          dataedit = {'editid':id, 'values':values, 'parent_id':parent_id, 'list_id':list_id, 'data':data};

          $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
          });
          $.ajax({
            type : 'POST',
            url : '{{route("insertoptions")}}',
            data : dataedit,
            dataType : 'json',
            success : function(response)
            {
                alert(response.msg);
                displayAjaxCols(response.listid);
            }
          });

        });
    }

    /* DELETE DROPDOWN OPTIONS */
    function delOption()
    {
      $("body").on("click",".deloption",function(){
          var opt = $(this).attr('id');
          $('#'+opt).remove();
          $('.'+opt).remove();
      });
    }

    /* ADD FIELD */
    function addCols(){
      $("body").on('click','.add-field',function(){
        var type = $("#type_fields").val();
        var len = $(".colfields").length;
        var idlist = $("input[name='idlist']").val();
        $("input[name='field_list'], input[name='dropdownlist']").val(idlist);
       
        if(len < 5 && type == 1)
        {
            //$("#cid").show();
            $("#openFields").modal();
        } 
        else if(len < 5 && type == 2) {
            $("#openDropdown").modal();
        }
        else 
        {
            alert(limit);
        }

      });
    } 


    function addFields()
    {
       $("body").on('click','.add-field-column',function(){
           var len = $(".colfields").length;
           var box_html;
           box_html = '<div class="col-md-12 row field-pos-'+len+' field-col"><input name="fields[]" class="cidlen form-control mb-2 col-md-6 colfields fieldinput field-pos-'+len+'" /><a id="field-pos-'+len+'" class="del_fields field-col mb-2 col-md-2 btn btn-warning field-pos-'+len+'">Delete</a><select class="field-pos-'+len+' form-control col-md-3 field-col" name="is_option[]"><option value="0">Optional</option><option value="1">Require</option></select></div>';

          if(len < 5)
          {
              $("#append_fields").append(box_html);
          } 
          else 
          {
              alert(limit);
          }
          
       });
    }

    /* SAVE FIELDS */
    function insertFields()
    {
        $("body").on("submit","#addFieldsForm",function(e)
        {
            e.preventDefault();
            var data = $(this).serialize();
            var len = $(".colfields").length;
            var inputlen = $(".fieldinput").length;
            var valid = [];
            var duplicated = 0;

            var check = $(".colfields");
            check.each(function(i, result){
               valid.push($(result).val());
            });

            var recipientsArray = valid.sort(); 
            var reportRecipientsDuplicate = [];
            for (var i = 0; i < recipientsArray.length - 1; i++) {
                if (recipientsArray[i + 1] == recipientsArray[i]) {
                    duplicated = 1;
                }
            }

            if(len > 5)
            {
               alert(limit);
            }
            else if(inputlen < 1)
            {
               alert("You should create at least 1 input");
            }
            else if(duplicated == 1)
            {
               alert("Input field cannot be same");
            }
            else 
            {
                $("#cfd").html("Loading...");
                $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                });
                 $.ajax({
                    type : 'POST',
                    url : '{{route("insertfields")}}',
                    data : data,
                    dataType : "json",
                    success : function(result){
                      $("#cfd").html("Create New Fields");
                      if(result.error == false)
                      {
                           if(result.listid.length > 0)
                            {
                               displayAjaxCols(result.listid);
                            }
                      }
                     
                      if(result.error == true)
                      {
                        $(".errfield").html('<div class="alert alert-danger">'+result.msg+'</div>');
                      } else {
                        $(".field-col").remove();
                        alert(result.msg);
                      }
                    }
                });
            }
            
        });
    }

    /* ADD DROPDOWN */
    function addDropdown()
    {
        $("body").on("click",".add-option",function(){
            var flen = $(".colfields").length;
            var len = $(".doption").length;
            var checkdropdown = $("input[name='dropdowname']").val();
            var valid = 1;

            var check = $(".colfields");
            check.each(function(i, result){
                if($(result).val() == checkdropdown)
                {
                    valid = 0;
                }
                //console.log($(result).val());
            });

            var dropdown = '<input name="doptions[]" class="form-control mb-2 col-sm-8 float-left doption opt-'+len+'" /><a id="opt-'+len+'" class="deloption mb-2 col-sm-3 btn btn-warning">Delete</a>';

            if(flen < 5 && valid == 1 && checkdropdown.length > 0)
            {
                $("#appendoption").append(dropdown);
            } 
            else if(checkdropdown.length == 0)
            {
                alert('Field cannot be empty');
            } 
            else if(valid == 0) 
            {
                alert('Field value cannot be same');
            }
            else 
            {
                alert(limit);
            }
        });
    }

    /* SAVE DROPDOWN */
    function insertDropdown()
    {
         $("body").on("click","#cdp",function(e){
            e.preventDefault();
            var len = $(".colfields").length;
            var data = $("#dropdownForms").serialize();
            var opt = $(".doption").length;
            if(len >= 5)
            {
                alert(limit);
            }
            else if(opt < 1)
            {
                alert('You should create at least 1 input');
            }
            else
            {
              $("#div-loading").show();
              $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                });
                $.ajax({
                  type : 'POST',
                  url : '{{route("insertdropdown")}}',
                  data : data,
                  dataType : 'json',
                  success : function(response)
                  {
                      $("#div-loading").hide();
                      alert(response.msg);
                      $(".doption, .deloption").remove();

                      if(response.listid.length > 0)
                      {
                         displayAjaxCols(response.listid);
                      }
                  }
                });
            }
         });
    }


    /*
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
            var box_html = '<div class="col-md-9 row"><input pos="'+len+'" class="fields pos-'+len+' form-control col-sm-6 toggledropdown cidlen" value="'+optionName+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning pos-'+len+'">Delete</a><div style="padding : 0" class="pos-'+len+' col-sm-9 togglepos-'+len+' hiddendropdown mb-2">'+options+'</div></div>';
            
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
    */

    /*function saveFields(){
        $("#cid").click(function(){
            var data = {};
            var len = $(".fields").length;
            var dlen;
            var dropfields;

            // Fields 
            for(x=0;x<len;x++)
            {
               var opt = [];
               var fields = $(".fields").eq(x).val();
               var idfields = $(".fields").eq(x).attr('id');
               var is_option = $(".sel_is_option").eq(x).val();
               var list_id = $("input[name='idlist']").val();

               //dropfield 
               var posfields = $(".fields").eq(x).attr('pos');
               dlen = $(".dropfield-"+posfields).length;

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

    */


    function displayDropdownMenu()
    {
        $("body").on("click",".toggledropdown",function(){
            var id = $(this).attr('pos');
            $(".togglepos-"+id).slideToggle();
        });
    }

    /* DELETE LISTS */
    function delEditor(){
      $("body").on("click",".del",function(){
        var q = confirm('Are you sure to delete?');
        var id = $(this).attr('id');

        if(q == true){
           $("#div-loading").show();
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

    /* DELETE FIELDS */
    function delCols(){
      $("body").on("click",".del_fields",function(){
        var len = $(".cidlen").length;
        var pos = $(this).attr('id');
        var id_attribute = $(this).attr('idbase');
        var listid = $(this).attr('listid');

        if(id_attribute !== undefined && listid !== undefined)
        {
          var conf = confirm('Are you sure want to delete this fields?');
          if(conf == true)
            {
              $.ajax({
                type : 'GET',
                url : '{{route("delfield")}}',
                data : {'id':id_attribute, 'list_id':listid},
                success : function(response){
                  alert(response.msg);
                  displayAjaxCols(response.listid);
                }
              });
            } 
            else 
            {
              return false;
            }
        } else {
          $("."+pos).remove();
          $("#"+pos).remove();
          $(".pos-"+pos).remove();
        }

      });
    }  

</script>
@endsection
