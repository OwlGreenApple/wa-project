@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-list-data">
  <div class="left">
    <h2>TEST LIST 1</h2>
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
                <input type="text" class="form-control" placeholder="Click to add message" >
                <span class="input-group-append">
                  <div class="btn border-left-0 border edit-icon">
                      <span class="icon-edit"></span>
                  </div>
                </span>
            </div>

            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Input your name" >
            </div> 

            <div class="input-group form-group text-left prep2">
               <!-- display radio button -->
            </div> 

            <div class="input-group form-group">
                <input type="text" class="form-control" placeholder="Email" >
            </div>
        </div>
        <!-- end wrapper -->

         <!-- outer wrapper -->
        <div class="outer-wrapper">
          <div class="form-row">
            <div class="form-group col-md-11">
              <input type="text" class="form-control" placeholder="Custom fields"/>
            </div>
            <div class="form-group col-md-1">
              <button type="button" class="btn btn-form"><span class="icon-delete"></span></button>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-2">
              <div class="relativity">
               <select class="form-control custom-select">
                  <option>Type</option>
                  <option>...</option>
               </select>
               <span class="icon-carret-down-circle"></span>
              </div>
            </div>

            <div class="form-group col-md-9">
              <input type="text" class="form-control" placeholder="Field Names"/>
            </div>
            <div class="form-group col-md-1">
              <button type="button" class="btn btn-form"><span class="icon-add"></span></button>
            </div>
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

<script type="text/javascript">
  $(document).ready(function() {    
     tabs();
     Choose();
     radioCheck();
     openImport();
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

  function radioCheck(){
      $("#tab2, #tab-contact").click(function(){
        $(".move_radio").prependTo($(".prep1"));
      });

      $("#tab3, #tab-form").click(function(){
        $(".move_radio").prependTo($(".prep2"));
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

</script>

@endsection
