@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-list-data">
  <div class="left">
    <h2>{{$label}}</h2>
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

          <!-- if contact added successfully -->
          <table class="table table-bordered mt-4">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Phone</th>
                <th>Username</th>
                <th>Email</th>
              </tr>
            </thead>
          </table>
      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 2 -->
    <div class="tabs-container" id="tab2C">
      <div class="act-tel-tab">
        <div class="form-control wrapper message">
          If you want add contact more than 1 please click : "<b><a class="open_import">import contact</a></b>" <!--or "<b>take from group</b>" if you want -->
        </div>

        <form class="wrapper add-contact">
            <div class="form-group">
              <label>Name:</label>
              <input type="text" class="form-control" placeholder="Input Your Name" >
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

                <input type="text" name="phone" class="form-control cphone" placeholder="Input your phone">
                <input type="text" name="usertel" class="form-control ctel" placeholder="Input your Telegram username">
              </div>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="text" class="form-control" placeholder="Input Your Email" />
            </div>

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

        <div class="wrapper">
          <form class="form-contact">
            <div class="input-group form-group">
              <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
            </div>

            <div class="input-group form-group">
                <input type="text" class="form-control" value="{{ $label }}" placeholder="Input List name" >
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

          <div id="append" class="form-row">
             <!-- display input here -->
          </div> 

        </div>
        <!-- end outer wrapper -->

        <!-- middle wrapper -->
        <div class="wrapper">
          <div class="form-group text-left">
             <label>Pixel</label>
             <textarea class="form-control"></textarea>
          </div>
          
          <div class="text-right">
            <button type="submit" class="btn btn-custom">Save Form</button>
          </div>

        </form>
        </div>
        <!-- end middle wrapper -->

        <!-- last wrapper -->
        <div class="wrapper">
          <div class="form-group text-left">
             <label class="col-md-12 row">FORM URL&nbsp;&nbsp;<span class="icon-copy"></span></label>
             <input type="text" class="form-control-lg" value="http://activtele.com/zkkdai"/>
          </div>
          
          <div class="form-group text-left">
             <label>COPY / PASTE on your Site&nbsp;&nbsp;<span class="icon-copy"></span></label>
             <textarea class="form-control" readonly="readonly"><form action="https://celebmail.id/mail/index.php/lists/tw895x290bc64/subscribe" method="post" accept-charset="utf-8" target="_blank">

                  <div class="form-group">
                  <label>Email <span class="required">*</span></label>
                  <input type="text" class="form-control" name="EMAIL" placeholder="" value="" required />
                  </div>

                  <div class="form-group">
                  <label>Telephone</label>
                  <input type="text" class="form-control" name="TELP" placeholder="" value=""/>
                  </div>

                  <div class="form-group">
                  <label>Name</label>
                  <input type="text" class="form-control" name="NAME" placeholder="" value=""/>
                  </div>

                  <div class="form-group">
                  <label>First name</label>
                  <input type="text" class="form-control" name="FNAME" placeholder="" value=""/>
                  </div>

                     <div class="clearfix"><!-- --></div>
                     <div class="actions">
                     <button type="submit" class="btn btn-primary btn-flat">Subscribe</button>
                     </div>
                     <div class="clearfix"><!-- --></div>

                  </form>
              </textarea>
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
                      <input class="form-control" name="import_file" type="file" />
                    <span><i>Please .csv only</i></span>

                    <div><a>Download Example CSV</a></div>

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

  <!-- Modal Dropdown -->
  <div class="modal fade" id="openDropdown" role="dialog">
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
  <!-- End Modal -->

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
    Choose();
    openImport();
    //column -- edit
    addCols();
    delCols();
    addDropdown();
    delDrop();
    addDropdownToField();
    displayDropdownMenu();
    delOption();
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
        }
        else {
          $("input[name=phone]").prop('disabled',true);
          $("input[name=usertel]").prop('disabled',false);
          $(".cphone").hide();
          $(".ctel").show();
        }
    });
  }

  function openImport() {
    $(".open_import").click(function(){
      $("#import-contact").modal();
    });
  }

  /* Column Additional */



  function addCols(){
      $("body").on('click','.add-field',function(){
        var len = $(".fields").length;
        var type = $("#type_fields").val();
        var box_html = '';

        box_html += '<div class="form-group col-md-8 pos-'+len+'">';
        box_html += '<input name="fields[]" class="form-control fields pos-'+len+'" />';
        box_html += '</div>';
        box_html += '<div class="form-group col-md-3 pos-'+len+'">';
        box_html += '<select name="isoption[]" class="pos-'+len+' form-control"><option value="0">Optional</option><option value="1">Require</option></select></div>';
        box_html += '</div>';
        box_html += '<div class="form-group col-md-1 pos-'+len+'">';
        box_html += '<button type="button" id="'+len+'" class="del btn btn-form"><span class="icon-delete"></span></button>';
        box_html += '</div>';

         /*box_html = '<div class="col-md-3 col-form-label text-md-right pos-'+len+'"></div><div class="col-md-9 row pos-'+len+'"><input name="fields[]" class="form-control mb-2 col-md-6 fields pos-'+len+'" /><a id="'+len+'" class="del mb-2 col-md-2 btn btn-warning">Delete</a><select name="isoption[]" class="pos-'+len+' form-control col-md-3"><option value="0">Optional</option><option value="1">Require</option></select></div>'; */
       
        if(len < 5 && type == 1){
            $("#append").append(box_html);
        } else if(len < 5 && type == 2) {
            $("#openDropdown").modal();
        }
        else {
            alert('You only can create 5 inputs');
        }
      });
    }

   function delCols(){
      $("body").on("click",".del",function(){
        var len = $(".fields").length;
        var pos = $(this).attr('id');
        $(".pos-"+pos).remove();
        $("#"+pos).remove();
      });
   }

  function addDropdown()
  {
      $("body").on("click",".add-option",function(){
          var flen = $(".fields").length;
          var len = $(".doption").length;
          var dropdown = '<input name="doption[]" class="form-control mb-2 col-sm-8 float-left doption dpos-'+len+'" /><a id="dpos-'+len+'" class="deloption mb-2 col-sm-3 btn btn-warning">Delete</a>';

          if(flen < 5){
              $("#appendoption").append(dropdown);
          } else {
              alert('You only can create 5 inputs');
          }
      });
  }

  function delDrop(){
      $("body").on("click",".deloption",function(){
        var len = $(".doption").length;
        var dpos = $(this).attr('id');
        $("."+dpos).remove();
        $("#"+dpos).remove();

        if(len == 1){
            $("#cip").hide();
        } 

      });
  }

  function addDropdownToField()
  {
       $("body").on("click","#cdp",function(){
          var len = $(".fields").length;
          var options = '';
          var optionName = $("#dropdown_name").val();
          $(".doption").each(function(){
              value = $(this).val();
              options += '<input name="dropfields['+len+'][]" class="form-control" value="'+value+'"/>';
          });
          var box_html = '<label class="col-md-3"></label> <div class="col-md-9 row"><input name="dropdown[]" pos="'+len+'" class="fields pos-'+len+' form-control col-sm-6 toggledropdown" value="'+optionName+'" /><a id="'+len+'" class="del mb-2 col-sm-3 btn btn-warning">Delete</a><div style="padding : 0" id="togglepos-'+len+'" class="pos-'+len+' col-sm-9 hiddendropdown mb-2">'+options+'</div></div>';
          
          if(len < 5)
          {
              $("#append").append(box_html);
              $(".doption, .deloption").remove();
          }
          else 
          {
              alert('You only can create 5 inputs');
          }

       });
  }

  function displayDropdownMenu()
  {
    $("body").on("click",".toggledropdown",function(){
        var id = $(this).attr('pos');
        $("#togglepos-"+id).slideToggle();
    });
  }   

  function delOption()
  {
    $("body").on("click",".deloption",function(){
        var opt = $(this).attr('id');
        $('#'+opt).remove();
        $('.'+opt).remove();
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
