@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-list-data">
  <div class="left">
    <h2>{{$label}}</h2>
    <h4 class="form-control">{{$url}}</h4>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container">
  <ul id="tabs" class="row">
      <li class="col-lg-4"><a id="tab1">Contact</a></li>
      <li class="col-lg-4"><a id="tab2">Add Contact</a></li>
      <li class="col-lg-4"><a id="tab3">Form</a></li>
  </ul>

  <!-- TABS CONTAINER -->
  <div class="tabs-content">
    <!-- TABS 1 -->
    <div class="tabs-container" id="tab1C">
      <div class="act-tel-tab">
          <h2>Add Your Contact</h2>
          <h6 class="mt-3">From <a id="tab-contact">Add Contact</a> or <a id="tab-form">Form</a></h6>
      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 2 -->
    <div class="tabs-container" id="tab2C">
      <div class="act-tel-tab">
        <div class="form-control wrapper message mimport">
          If you want add contact more than 1 please click : "<b><a class="open_import">import contact</a></b>" <!--or "<b>take from group</b>" if you want -->
        </div>

        <div class="error_message"><!-- error --></div>
        <div class="main"><!-- error --></div>
        <form class="wrapper add-contact">
            <div class="form-group">
              <label>Name:</label>
              <input type="text" name="subscribername" class="form-control" placeholder="Input Your Name" >
              <span class="error name"></span>
            </div>

            <div class="prep1">
              <div class="input-group mt-4 mb-3 move_radio">
                <div class="input-group-prepend">
                  <button class="btn btn-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Telegram Contact</button>
                  <div class="dropdown-menu">
                    <a class="dropdown-item" id="ph">Phone</a>
                    <a class="dropdown-item" id="tl">Telegram Username</a>
                  </div>
                </div>

                <input type="hidden" name="selectType" id="selectType" value="ph">
                <input type="text" name="phone" class="form-control cphone" placeholder="Input your phone">
                <input type="text" name="usertel" class="form-control ctel" placeholder="Input your Telegram username">
              </div>
              <span class="error phone"></span>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" class="form-control" placeholder="Input Your Email" />
              <span class="error email"></span>
            </div>

            <input type="hidden" name="listname" value="{{$listname}}">
            <input type="hidden" name="listid" value="{{ $listid }}">

            <div class="text-right">
              <button type="submit" class="btn btn-custom">Add Contact</button>
            </div>
        </form>
      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 3 -->
    <div class="tabs-container" id="tab3C">
      <div class="act-tel-tab">
        <div class="wrapper">
          <div class="form-control col-lg-6 message">
            <sb>Saved, click to copy link from</sb> <a class="icon-copy"></a>
          </div>
        </div>

        <form class="form-contact" id="edit_list">

          <div class="wrapper">
            <div class="form-contact">
              <div class="input-group form-group">
                <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
              </div>

              <div class="input-group form-group">
                  <input type="text" name="list_label" class="form-control" placeholder="Input List name" value="{{$label}}"/>
                  <div class="error list_label col-lg-12 text-left"></div>
              </div> 
          </div>
          <!-- end wrapper -->

           <!-- outer wrapper -->
          <div class="outer-wrapper">
            <div class="form-row">
              <div class="form-group col-md-3 py-2">
                <h6>Custom Fields</h6>
              </div>

              <div class="form-group col-md-8">
                <div class="relativity">
                   <select id="type_fields" class="form-control custom-select">
                      <option value="1">Fields</option>
                      <option value="2">Dropdown</option>
                   </select>
                   <span class="icon-carret-down-circle"></span>
                </div>
              </div>
              <div class="form-group col-md-1">
                <button type="button" class="btn btn-form add-field"><span class="icon-add"></span></button>
              </div>
            </div>

            <div id="additional" class="form-row">
                <!-- additional -->
            </div>

          </div>
          <!-- end outer wrapper -->

          <!-- middle wrapper -->
          <div class="wrapper">
            <div class="form-group text-left">
               <label>Pixel</label>
               <textarea name="pixel" class="form-control"></textarea>
            </div>
            
            <div class="text-right">
              <button type="submit" class="btn btn-custom">Add Data</button>
            </div>

        </form>
          
        </div>
        <!-- end middle wrapper -->

        <!-- last wrapper -->
        <div class="wrapper">
          <div class="form-group text-left">
             <label class="col-md-12 row">FORM URL&nbsp;&nbsp;<a data-link="{{$url}}" class="icon-copy btn-copy"></a></label>
             <input id="linkcopy" value="{{$url}}" type="text" class="form-control-lg" />
          </div>
          
          <div class="form-group text-left">
              <label>COPY / PASTE on your Site&nbsp;&nbsp;<a class="structure-form"><span class="icon-copy"></span></a></label>
              <textarea class="form-control" id="structure-form" readonly="readonly"><iframe src="{{url($listname)}}" style="border:0px #ffffff none;" name="myiFrame" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" height="400px" width="600px" allowfullscreen></iframe></textarea>
          </div>
        </div>
        <!-- end last wrapper -->

      </div>
    <!-- end tabs -->    
    </div>

  </div>
</div>

<!-- Modal Import Contact -->
  <div class="modal fade child-modal" id="import-contact" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2">
                  <form>
                    <label>Import Contact</label>
                      <input class="form-control" name="csv_file" type="file" />
                      <input type="hidden" name="list_id_import" value="{{ $id }}" />
                    <span><i>Please .csv only</i></span>

                    <div><a href="{{ asset('assets/csv/csv-example.csv') }}">Download Example CSV</a></div>

                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Import</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
               
            </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

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
                    <input type="hidden" value="{{$id}}" name="field_list"/>
                    <div class="form-group">
                       <button id="cfd" class="btn btn-success btn-sm">Create Fields</button>
                       <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Close</button>
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
                      <div id="appendoption" class="form-group">
                         <!-- display input here -->
                      </div> 
                      <input type="hidden" name="dropdownlist"/>
                      <div class="form-group">
                         <button id="cdp" class="btn btn-success btn-sm">Create Dropdown</button>
                         <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Close</button>
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
                    <input class="btn btn-primary btn-sm add-edit-option" type="button" value="Add Option" />
                </div>
               
                <label>Option List</label>
                <form id="optionform">
                    <div id="editoptions" class="form-group">
                       <!-- display input here -->
                    </div> 

                    <input type="hidden" name="parent_id"/>
                    <input type="hidden" name="list_id"/>
                    <div class="form-group">
                       <button id="edp" class="btn btn-success btn-sm">Edit Dropdown</button>
                       <button type="button" data-dismiss="modal" class="btn btn-default btn-sm">Close</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      
    </div>
  </div>

  <!-- Modal Copy Link -->
<div class="modal fade" id="copy-link" role="dialog">
  <div class="modal-dialog">
    
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modaltitle">
          Copy Link
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        You have copied the link!
      </div>
      <div class="modal-footer" id="foot">
        <button class="btn btn-primary" data-dismiss="modal">
          OK
        </button>
      </div>
    </div>
      
  </div>
</div>

<script type="text/javascript">

  /* CKEditor */
  CKEDITOR.replace( 'editor1',{
      allowedContent: true,
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

  $(document).ready(function() {    
    tabs();
    table();
    Choose();
    openImport();
    csvImport();
    addContact();
    //column -- edit
    displayAdditional();
    updateList();
    delCols();
    addCols();
    addFields();
    insertDropdown();
    addDropdown();
    editOption();
    addOption();
    insertOption();
    delOption();
    insertFields();
    openAdditional();
    copyLink();
  });

  // Jquery Tabs
  function tabs() {    
      $('#tabs li a:not(:first)').addClass('inactive');
      $('.tabs-container').hide();
      $('.tabs-container:first').show();

      $('#tabs li a').click(function(){
        var t = $(this).attr('id');
        if($(this).hasClass('inactive')){ //this is the start of our condition 
          $('#tabs li a').addClass('inactive');
          $(this).removeClass('inactive');

          $('.tabs-container').hide();
          $('#'+ t + 'C').fadeIn('slow');
        }
      });

      $("#tab-contact").click(function(){
        $("#tab1").addClass('inactive');
        $("#tab2").removeClass('inactive');

        $('.tabs-container').hide();
        $('#tab2C').fadeIn('slow');
      }); 

      $("#tab-form").click(function(){
         $("#tab1").addClass('inactive');
         $("#tab3").removeClass('inactive');

         $('.tabs-container').hide();
         $('#tab3C').fadeIn('slow');
      });
  }

  function table(){
      $("#datasubscriber").DataTable({
          "pageLength": 5
      });
  }

  function Choose(){
    $("input[name=usertel]").prop('disabled',true);
    $(".ctel").hide();

    $(".dropdown-item").click(function(){
       var val = $(this).attr('id');

       if(val == 'ph')
        {
          $("input[name=phone]").prop('disabled',false);
          $("input[name=usertel]").prop('disabled',true);
          $(".cphone").show();
          $(".ctel").hide();
          $("#selectType").val("ph");
        }
        else {
          $("input[name=phone]").prop('disabled',true);
          $("input[name=usertel]").prop('disabled',false);
          $(".cphone").hide();
          $(".ctel").show();
          $("#selectType").val("tl");
        }
    });
  }

  function openImport() {
    $(".open_import").click(function(){
      $("#import-contact").modal();
    });
  }

  function csvImport()
  {
    $("body").on('submit','#importform',function(e){
        e.preventDefault();
        var data = new FormData($(this)[0]);
        $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
        });
        $.ajax({
            type : 'POST',
            url : "{{ url('import_csv_list_subscriber') }}",
            data : data,
            contentType: false,
            processData: false,
            beforeSend: function()
            {
              $('#loader').show();
              $('.div-loading').addClass('background-load');
            },
            success : function(result){
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');
              $('input[name="csv_file"]').val('');
              alert(result.message);
            },
            error: function (xhr, ajaxOptions, thrownError) {
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');
              var err = eval("(" + xhr.responseText + ")");
              var msg = '';
              for ( var property in err.errors ) {
                msg += err.errors[property][0]+"\n"; // Outputs: foo, fiz or fiz, foo
              }
              alert(msg);
              $('input[name="csv_file"]').val('');
            }
        });/* end ajax */
    });
  }

  function addContact(){
    $(".add-contact").submit(function(e){
        e.preventDefault();
        var data = $(this).serialize();
        //$("#submit").html('<img src="{{asset('assets/css/loading.gif')}}"/>');
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
            type : "POST",
            url : "{{ route('savesubscriber') }}",
            data : data,
            beforeSend: function()
            {
              $('#loader').show();
              $('.div-loading').addClass('background-load');
            },
            success : function(result){
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');

              if(result.success == true){
                  alert(result.message);
                  clearField();
              } else {
                  $(".error").fadeIn('fast');
                  $(".name").text(result.name);
                  $(".main").text(result.main);
                  $(".main").text(result.list);
                  $(".email").text(result.email);
                  $(".phone").text(result.phone);
                  $(".phone").text(result.usertel);

                  if(result.message !== undefined){
                       $(".error_message").html('<div class="alert alert-danger text-center">'+result.message+'</div>');
                  }

                  $(".error").delay(2000).fadeOut(5000);
              }
            }
        });
        /*end ajax*/
      });
  }

  /*** Column Additional ***/

  var limit = 'You only can create 5 fields only';

    function displayAdditional(){
        $.ajax({
            type : 'GET',
            data : {'id': {!! $id !!}},
            url : "{{route('additionalList')}}",
            dataType : "json",
            success : function(result){
               var box_html = '';
               var is_option = {};
               var options = '';

               $.each(result.additional,function(key, value){
                  var len = key;
                  // dropdown
                  if(value.is_field == 1 && value.id_parent == 0)
                  {

                    box_html += '<div class="form-group col-md-8 pos-'+len+' dropdown">';
                    box_html += '<input field="1" id='+value.id+' pos="'+value.id+'" class="cidlen colfields dropfields form-control" value="'+value.name+'" />';
                    box_html += '</div>';
                    box_html += '<div class="form-group col-md-3 pos-'+len+'">';
                    box_html += '<a class="btn btn-form edit-option" id="'+value.id+'" list_id = '+value.list_id+'>Edit Option</a>';
                    box_html += '</div>';
                    box_html += '<div class="form-group col-md-1 pos-'+len+'">';
                    box_html += '<a id="'+len+'" class="del_fields btn btn-form" idbase = '+value.id+' listid = '+value.list_id+'><span class="icon-delete"></span></a>';
                    box_html += '</div>';

                   /*box_html += '<div class="col-md-9 row dropdown pos-'+len+'"><input field="1" id='+value.id+' pos="'+value.id+'" class="cidlen colfields dropfields form-control col-sm-6" value="'+value.name+'" /><a id="'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><a class="btn btn-info col-sm-2 mb-2 btn-sm edit-option" id="'+value.id+'" list_id = '+value.list_id+'>Edit Option</a></div>';
                   */
                  }

                  if(value.is_field == 0 && value.id_parent == 0)
                  {

                    box_html += '<div class="form-group col-md-8 pos-'+len+'">';
                    box_html += '<input field="0" id='+value.id+' name="field[]" class="cidlen form-control fields colfields pos-'+len+'" value="'+value.name+'" />';
                    box_html += '</div>';
                    box_html += '<div class="form-group col-md-3 pos-'+len+'">';
                    box_html += '<select name="is_option[]" class="is_option pos-'+len+' form-control selopt-'+len+'"><option value="0">Optional</option><option value="1">Require</option></select></div>';
                    box_html += '</div>';
                    box_html += '<div class="form-group col-md-1 pos-'+len+'">';
                    box_html += '<a id="'+len+'" class="pos-'+len+' del_fields btn btn-form" idbase = '+value.id+' listid = '+value.list_id+'><span class="icon-delete"></span></a>';
                    box_html += '</div>';

                     /*box_html += '<div class="col-md-3 text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input field="0" id='+value.id+' name="field[]" class="cidlen form-control mb-2 col-md-6 fields colfields pos-'+len+'" value="'+value.name+'" /><a id="'+len+'" class="del_fields pos-'+len+' mb-2 col-md-2 btn btn-warning" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a><select name="is_option[]" class="is_option pos-'+len+' form-control col-md-3 selopt-'+len+'"><option value="0">Optional</option><option value="1">Require</option></select></div>';
                       is_option[len] = value.is_optional;
                    */
                    is_option[len] = value.is_optional;
                  }  
                 
               });

               $("#additional").html(box_html);
               $.each(is_option,function(key, value){
                  $(".selopt-"+key+"").val(value);
               });
               //CKEDITOR.instances.editor1.setData( result.content );
               var clen = $(".fields").length;
                if(clen == 0)
                {
                  $("#cid").hide();
                } else {
                  $("#cid").show();
                }
            }
        });
    }

    /* EDIT OR UPDATE LIST */
    function updateList(){
        $("#edit_list").submit(function(e){
            e.preventDefault();
             var databutton = $("input[name='page_position']").val(); // get data button position
             databutton = parseInt(databutton) -1;
            
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
                id : {!! $id !!},
                list_label : $("input[name='list_label']").val(),
                editor : CKEDITOR.instances.editor1.getData(),
                pixel : $("textarea[name='pixel']").val(),
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
                url : '{{route("listupdate")}}',
                data : data,
                dataType : "json",
                beforeSend: function()
                {
                  $('#loader').show();
                  $('.div-loading').addClass('background-load');
                },
                success : function(result){
                   $('#loader').hide();
                   $('.div-loading').removeClass('background-load');


                   if(result.error == undefined)
                   {
                      $(".list_label").html('');
                      alert(result.message);
                      //displayAjaxCols(result.listid);
                      displayAdditional();
                   }
                   else if(result.additionalerror == true)
                   {
                      alert(result.message);
                   }
                   else
                   {
                      $(".list_label").html(result.label);
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

    /* EDIT DROPDOWN OPTIONS */
    function editOption()
    {
      $("body").on("click",".edit-option",function(){
         var id = $(this).attr('id');
         var box_html = '';

         $("#editDropdown").modal();
         $("input[name='parent_id']").val(id);
         $("input[name='list_id']").val({!! $id !!});

         $.ajax({
            type : 'GET',
            url : '{{route("editdropfields")}}',
            data : {'id':id},
            dataType : 'json',
            success : function(result)
            {
               $.each(result.dropfields,function(key, value){
                  var len = key;
                  box_html += '<input id='+value.id+' class="dropdownopt form-control mb-2 col-sm-9 float-left doption opt-'+len+'" value="'+value.name+'" />';
                  box_html += '<a id="opt-'+len+'" class="del_fields mb-2 col-sm-2 btn btn-danger deloption" idbase = '+value.id+' listid = '+value.list_id+'><span class="icon-delete"></span></a>';
                  /*
                  box_html += '<input id='+value.id+' class="dropdownopt form-control mb-2 col-sm-8 float-left doption opt-'+len+'" value="'+value.name+'" /><a id="opt-'+len+'" class="del_fields mb-2 col-sm-3 btn btn-warning deloption" idbase = '+value.id+' listid = '+value.list_id+'>Delete</a>';
                  */
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
            var dropdownOptions = '<input class="newoption form-control mb-2 col-sm-9 float-left doption opt-'+len+'" /><a id="opt-'+len+'" class="deloption mb-2 col-sm-2 btn btn-danger"><span class="icon-delete"></span></a>';

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
            beforeSend: function()
            {
              $('#loader').show();
              $('.div-loading').addClass('background-load');
            },
            success : function(response)
            {
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');

              alert(response.msg);
              //displayAjaxCols(response.listid);
              displayAdditional();
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
        $("input[name='field_list'], input[name='dropdownlist']").val({!! $id !!});
       
        if(type == 1)
        {
            //$("#cid").show();
            $("#openFields").modal();
        } 
        else {
             $("#openDropdown").modal();
        }

        /*
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
        */
      });
    } 


    function addFields()
    {
       $("body").on('click','.add-field-column',function(){
           var len = $(".colfields").length;
           var box_html = '';

           box_html += '<div class="col-md-12 row field-pos-'+len+' field-col">';
           box_html += '<input name="fields[]" class="cidlen form-control mb-2 col-md-6 colfields fieldinput field-pos-'+len+'" />';
           box_html += '<select class="field-pos-'+len+' form-control col-md-3 field-col" name="is_option[]"><option value="0">Optional</option><option value="1">Require</option></select>';
           box_html += '<a id="field-pos-'+len+'" class="del_fields field-col mb-2 col-md-2 btn btn-danger field-pos-'+len+'"><span class="icon-delete"></span></a>';
           box_html += '</div>';
           /*
           box_html = '<div class="col-md-12 row field-pos-'+len+' field-col"><input name="fields[]" class="cidlen form-control mb-2 col-md-6 colfields fieldinput field-pos-'+len+'" /><a id="field-pos-'+len+'" class="del_fields field-col mb-2 col-md-2 btn btn-warning field-pos-'+len+'">Delete</a><select class="field-pos-'+len+' form-control col-md-3 field-col" name="is_option[]"><option value="0">Optional</option><option value="1">Require</option></select></div>';
           */

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
                    beforeSend: function()
                    {
                      $('#loader').show();
                      $('.div-loading').addClass('background-load');
                    },
                    success : function(result){
                      $("#cfd").html("Create New Fields");
                      $('#loader').hide();
                      $('.div-loading').removeClass('background-load');
                      if(result.error == false)
                      {
                           if(result.listid.length > 0)
                            {
                               //displayAjaxCols(result.listid);
                               displayAdditional();
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

            var dropdown = '<input name="doptions[]" class="form-control mb-2 col-sm-9 float-left doption opt-'+len+'" /><a id="opt-'+len+'" class="deloption mb-2 col-sm-2 btn btn-warning"><span class="icon-delete"></span></a>';

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
                         //displayAjaxCols(response.listid);
                         displayAdditional();
                      }
                  }
                });
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
                beforeSend: function()
                {
                  $('#loader').show();
                  $('.div-loading').addClass('background-load');
                },
                success : function(response){
                  $('#loader').hide();
                  $('.div-loading').removeClass('background-load');

                  alert(response.msg);
                  //displayAjaxCols(response.listid);
                  displayAdditional();
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

    function copyLink(){
      $( ".btn-copy" ).click(function(e) 
      {
        e.preventDefault();
        e.stopPropagation();

        var link = $(this).attr("data-link");

        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $('#copy-link').modal('show');
      });
    }

  /*
  function radioCheck(){
      $("#tab2, #tab-contact").click(function(){
        $(".move_radio").prependTo($(".prep1"));
      });

      $("#tab3, #tab-form").click(function(){
        $(".move_radio").prependTo($(".prep2"));
      });
  }
  */

</script>

@endsection
