@extends('layouts.app')

@section('content')
<!-- Modal Delete Confirmation -->
<div class="modal fade" id="confirm-delete" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content content-premiumid">
      <div class="modal-header header-premiumid">
        <h5 class="modal-title" id="modaltitle">
          Confirmation Delete
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <input type="hidden" name="id_phone_number" id="id_phone_number">

        <label>Are you sure want to <i>delete</i> this data ?</label>
        <br><br>
        <span class="txt-mode"></span>
        <br>
        
        <div class="col-12 mb-4" style="margin-top: 30px">
          <button class="btn btn-danger btn-block btn-delete-ok" data-dismiss="modal" id="button-delete-phone">
            Yes, Delete Now
          </button>
        </div>
        
        <div class="col-12 text-center mb-4">
          <button class="btn  btn-block btn-delete-ok" data-dismiss="modal">
            Cancel
          </button>  
        </div>
      </div>
    </div>   
  </div>
</div>


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
        <div class="form-control message col-lg-9">
          Your Telegram Account Connected!
        </div>

        <div class="row col-fix">
            <form class="wrapper add-contact col-lg-9 pad-fix" id="form-connect">
                <div class="form-group row col-fix">
                  <label class="col-sm-3 col-form-label">Phone Telegram :</label>
                  <div class="col-sm-9">
                    <input type="text" id="phone_number" name="phone_number" class="form-control" />
                    <span class="error phone_number"></span>
                  </div>
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
              <input type="text" class="form-control col-sm-9" id="verify_code"/>
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
                  <th class="text-center">Status</th>
                  <th class="text-center">Edit</th>
                </tr>
              </thead>

              <tbody id="table-phone">
              </tbody>
            </table>
        </div>

      </div>
    <!-- end tabs -->  
    </div>

    <!-- TABS 2 -->
    <div class="tabs-container" id="tab2C">
      <div class="act-tel-settings">

      <div class="form-control message-settings col-lg-9 mb-4"><!-- --></div>

      <form id="user_contact" class="form-contact">
        <div class="wrapper account mb-5">
          <h5>Edit Your Personal Data</h5>   
              <div class="form-group row col-fix">
                <label class="col-sm-4 col-form-label">Email</label>
                <label class="col-sm-1 col-form-label">:</label>
                <div class="form-control col-sm-7 text-left">{{$user->email}}</div>
              </div> 

              <div class="form-group row col-fix">
                <label class="col-sm-4 col-form-label">Full Name</label>
                <label class="col-sm-1 col-form-label">:</label>
                <div class="col-sm-7 text-left row">
                  <input name="user_name" type="text" class="form-control" value="{{$user->name}}" />
                  <span class="error user_name"></span>
                </div>
                
              </div> 

              <div class="form-group row col-fix">
                <label class="col-sm-4 col-form-label">Phone Number</label>
                <label class="col-sm-1 col-form-label">:</label>
                <div class="col-sm-7 text-left row">
                  <input name="user_phone" type="text" class="form-control" value="{{$user->phone_number}}" />
                  <span class="error user_phone"></span>
                </div>
              </div>
        </div>
        
        <div class="wrapper account">
          <h5>Edit Your Password</h5>
              <div class="form-group row col-fix">
                <label class="col-sm-4 col-form-label">Old Password</label>
                <label class="col-sm-1 col-form-label">:</label>
                <div class="col-sm-7 text-left row">
                  <input type="password" name="oldpass" class="form-control" />
                  <span class="error oldpass"></span>
                </div>
              </div> 

              <div class="form-group row col-fix">
                <label class="col-sm-4 col-form-label">New Password</label>
                <label class="col-sm-1 col-form-label">:</label>
                <div class="col-sm-7 text-left row">
                  <input type="password" name="newpass" class="form-control" />
                  <span class="error newpass"></span>
                </div>
              </div> 

              <div class="form-group row col-fix">
                <label class="col-sm-4 col-form-label">Confirm New Password</label>
                <label class="col-sm-1 col-form-label">:</label>
                <div class="col-sm-7 text-left row">
                  <input type="password" name="confpass" class="form-control" />
                  <span class="error confpass"></span>
                </div>
              </div>

              <div class="text-right">
                <button type="submit" class="btn btn-custom">Update Account</button>
              </div>
        </div>
        </form>
        <!-- end wrapper -->

      </div>
    <!-- end tabs -->    
    </div>

  </div>
<!-- end container -->    
</div>

<!-- Modal Edit Phone -->
  <div class="modal fade child-modal" id="edit-phone" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="alert alert-danger"><!-- error --></div>
            <div class="form-group">
                 <div class="mb-2">
                  <form id="edit_phone_number">
                    <label>Edit Phone Number</label>
                    <div class="form-group">
                      <input type="text" class="form-control" name="edit_phone" />
                    </div>
                 
                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Save</button>
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

<script type="text/javascript">
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
  
  function loadPhoneNumber(){
    $.ajax({
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      type: 'GET',
      url: "<?php echo url('/load-phone-number');?>",
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
        $('#table-phone').html(data.view);
      }
    });
  }

  $(document).ready(function() {    
    tabs();
    loadPhoneNumber();
    editPhoneNumber();
    openEditModal();
    settingUser();

    $('#div-verify').hide();
    $('.message').hide();
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

          if(data.status == "success") {
            $('.message').show();
            $('.message').html(data.message);
            $('#div-verify').show();
            loadPhoneNumber();
          }

          if(data.status == "error") {
              $(".phone_number").html(data.phone_number);
          }

        }
      });
      
    });
    $('#button-verify').click(function(){
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: "<?php echo url('/verify-phone');?>",
        data: {
          phone_number : $("#phone_number").val(),
          verify_code : $("#verify_code").val(),
        },
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
          $('.message').show();
          $('.message').html(data.message);
          loadPhoneNumber();
        }
      });
    });
    $('#button-delete-phone').click(function(){
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: "<?php echo url('/delete-phone');?>",
        data: {
          id : $("#id_phone_number").val(),
        },
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
          $('.message').show();
          $('.message').html(data.message);
          loadPhoneNumber();
        }
      });
    });


    $('body').on("click","#link-resend",function(){
      var data_tel = $(this).attr('data-phone');
      var data = {'resend':1,'phone_number':data_tel};

      $.ajax({
        type: 'GET',
        url: "{{ url('connect-phone') }}",
        data: data,
        dataType: 'json',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
        
          if(result.status == "success") {
            $('.message').show();
            $('.message').html(result.message);
            $('#div-verify').show();
            loadPhoneNumber();
          }

          if(result.status == "error") {
              $(".phone_number").html(data.phone_number);
          }

        }
      });
      
    });

    $("body").on("click", ".icon-delete", function() {
      $('#id_phone_number').val($(this).attr('data-id'));
      $('#confirm-delete').modal('show');
    });
    $("body").on("click", ".link-verify", function() {
      $("#phone_number").val($(this).attr('data-phone'));
      $('#div-verify').show();
      $("#verify_code").focus();
    });
  });

  function settingUser(){
    $(".message-settings").hide();
    $("#user_contact").submit(function(e){
      e.preventDefault();
      var data = $(this).serialize();

      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : '{{url("save-settings")}}',
          data : data,
          dataType : 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            if(result.status == 'success'){
              $(".error").hide();
              $('.message-settings').show();
              $('.message-settings').html(result.message);
            }
            else if(result.status == 'error')
            {
              $(".error").show();
              $(".user_name").html(result.user_name);
              $(".user_phone").html(result.user_phone);
              $(".oldpass").html(result.oldpass);
              $(".confpass").html(result.confpass);
              $(".newpass").html(result.newpass);
            }
            else {
              $('.message-settings').show();
              $('.message-settings').html(result.message);
            }
          },error: function(xhr,attribute,throwable){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
          }
        });
     });
  }

  function openEditModal(){
    $("body").on("click",".btn-edit",function(){
      var number = $(this).attr('data-number');
      $("input[name='edit_phone']").val(number);
      $("#edit-phone").modal();
    });
  }

  function editPhoneNumber()
  {
     $(".alert").hide();
     $("#edit_phone_number").submit(function(e){
        e.preventDefault();
        var values = $(this).serialize();

        $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : '{{url("edit-phone")}}',
          data : values,
          dataType : 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            $('.message').show();
            $('.message').html(result.message);

            if(result.error == 'true'){
              $(".alert").show();
              $(".alert").html(result.message);
            }

            if(result.status == "success") {
              $('#div-verify').show();
              loadPhoneNumber();

              $("#phone_number").val(result.phone);
              $("#edit-phone").modal('hide');
              $(".alert").hide();
            }
          },error: function(xhr,attribute,throwable){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
          }
        });
     });
  }

</script>

@endsection
