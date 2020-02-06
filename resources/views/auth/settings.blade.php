@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>SETTINGS</h2>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container">
  <ul id="tabs" class="row">
      <li class="col-lg-4"><a id="tab1">Telegram Settings</a></li>
      <li class="col-lg-4"><a id="tab2">Account Settings</a></li>
  </ul>

  <!-- TABS CONTAINER -->
  <div class="tabs-content">

    <!-- TABS 1 -->
    <div class="tabs-container" id="tab1C">
      <div class="act-tel-settings">
        <div class="form-control message col-lg-6">
          Your Telegram Account Connected!
        </div>

        <div class="row col-fix">
            <form class="wrapper add-contact col-lg-9 pad-fix" id="form-connect">
                <div class="form-group row col-fix">
                  <label class="col-sm-3 col-form-label">Phone Telegram :</label>
                  <input type="text" class="form-control col-sm-9" />
                </div>

                <div class="text-right">
                  <button type="button" id="button-connect" class="btn btn-custom">Connect</button>
                </div>
            </form>

            <div class="col-lg-3 plan">
              <div>Current plan : <b>pro</b></div>
              <div>Valid Until 31 Dec 2020</div>
              <div><i>Buy More</i></div>
            </div>
        </div>

        <div class="wrapper verification" id="div-verify">
            <div class="form-group"><label class="col-sm-12 col-form-label">Input verification code from your <strong>Telegram Account</strong></label></div>
            <div class="form-group row col-fix">
              <label class="col-sm-3 col-form-label">Verification Code :</label>
              <input type="text" class="form-control col-sm-9" />
            </div>

            <div class="text-right">
              <button type="button" id="button-verify" class="btn btn-custom">Submit</button>
            </div>
        </div>

        <div class="wrapper add-contact">
            <table class="table table-bordered mt-4">
              <thead class="bg-dashboard">
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Phone Telegram</th>
                  <th class="text-center">Delete</th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td class="text-center">1</td>
                  <td class="text-center">08177728428</td>
                  <td class="text-center"><a class="icon icon-delete"></a></td>
                </tr>
              </tbody>
            </table>
        </div>

      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 2 -->
    <div class="tabs-container" id="tab2C">
      <div class="act-tel-settings">
       
        <div class="wrapper">
          <form class="form-contact">
              <div class="form-group row col-fix">
                <label class="col-sm-3 col-form-label">Password :</label>
                <input type="text" class="form-control col-sm-9" />
              </div> 

              <div class="form-group row col-fix">
                <label class="col-sm-3 col-form-label">Confirm Password :</label>
                <input type="text" class="form-control col-sm-9" />
              </div>

              <div class="text-right">
                <button type="submit" class="btn btn-custom">Save</button>
              </div>
          </form>
        </div>
        <!-- end wrapper -->

      </div>
    <!-- end tabs -->    
    </div>

  </div>
<!-- end container -->    
</div>

<script type="text/javascript">
  $(document).ready(function() {    
    tabs();
    $('#div-verify').hide();
    $('#button-connect').click(function(){
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: "<?php echo url('/connect-phone');?>",
        data: $("#form-connect").serialize(),
        dataType: 'text',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          var data = jQuery.parseJSON(result);
          $('#div-verify').show();
        }
      });
      
    });
    $('#button-verify').click(function(){
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: "<?php echo url('/verify-phone');?>",
        data: $("#form-connect").serialize(),
        dataType: 'text',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          var data = jQuery.parseJSON(result);
          $('#div-verify').show();
        }
      });
    });
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
  }

</script>

@endsection
