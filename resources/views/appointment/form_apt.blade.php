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

        <label><h4>Are you sure want to <i>delete</i> this phone number ?</h4></label>
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

<div class="container act-tel-apt">
      <h3 class="title">Form Appointment Reminder</h3>
      <div class="col-md-12 row">
        <input type="text" class="form-control" placeholder="Find a contact by phone number">
      </div>
      <form>
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Name</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 text-left row">
                <input type="password" name="oldpass" class="form-control" />
                <span class="error oldpass"></span>
              </div>
            </div> 

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Phone Number</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 text-left row">
                <input type="password" name="newpass" class="form-control" />
                <span class="error newpass"></span>
              </div>
            </div> 

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Choose Appointment Time :</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 relativity text-left row">
                <input id="datetimepicker-date" type="text" name="date_send" class="form-control custom-select-campaign" />
                <span class="icon-calendar"></span>
                <span class="error date_send"></span>
              </div>
            </div>

            <div class="text-left">
              <button type="submit" class="btn btn-custom">Submit</button>
            </div>

            <div class="text-left marketing">
              <div>Marketing by</div>
              <div><img src="{{asset('assets/img/marketing-logo.png')}}"/></div>
            </div>
      </form>
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
      },
      error: function(xhr,attr,throwable){
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        alert('Sorry cannot load phone list, please call administrator'); 
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
      var phone_number = $("#phone_number").val();
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: "{{ url('connect-phone') }}",
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
            $('.message').html('<div class="text-center">'+data.message+" <b><h5><span id='min'></span> : <span id='secs'></span></h5></b></div>");
            loadPhoneNumber();
            waitingTime();
          }

          if(data.status == "error") {
              $(".phone_number").html(data.phone_number);
              $('.message').show();
              $('.message').html(data.message);
          }

        },
        error: function(xhr,attr,throwable)
        {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            alert(xhr.responseText);
        }
      });
      
    });

    function waitingTime()
    {
        var sc = 0;
        var min = 0;
        var tm = setInterval(function(){
            $("#secs").html(sc);
            $("#min").html('0'+min);

            if(sc < 10)
            {
              $("#secs").html('0'+sc);
            }

            if(sc == 60){
              min = min + 1;
              $("#min").html('0'+min);
              sc = 0;
              $("#secs").html('0'+sc);
            }

            if(min == 6)
            {
                $("#secs").html('0'+0);
                clearInterval(tm);
            }

            sc++;
        },1000);
    };

    function getQRCode(phone_number)
    {
      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type: 'GET',
          url: "{{ url('verify-phone') }}",
          data: {
            phone_number : phone_number,
          },
          dataType: 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success: function(result) {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');

            if(result.status == 'error'){
              $('.message').show();
              $('.message').html(result.phone_number);
            }
            else
            {
              $('#div-verify').show();
              $("#qr-code").html(result.data);
              countDownTimer(phone_number);
            }
            
            loadPhoneNumber();
          },
          error : function(xhr,attr,throwable){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
            alert('Sorry, unable to display QR-CODE, there is something wrong with our server, please try again later')
          }
        });

    }

    function countDownTimer(phone_number)
    {
      var sec = 25; //countdown timer
      var word = '<h3>Please scan qr-code before time\'s up :</h3>';
      var timer = setInterval( function(){
                
                if(sec < 1){
                  clearInterval(timer);
                  checkQRcode(phone_number);
                }

                if(sec < 10){
                  $("#timer").html(word+'<h4><b>0'+sec+'</b></h4>');
                }
                else
                {
                  $("#timer").html(word+'<h4><b>'+sec+'</b></h4>');
                }
                sec--;
            },1000);
    }

    function checkQRcode(phone_number)
    {
      $.ajax({
        type: 'GET',
        url: "{{ url('check-qr') }}",
        data: {
          no_wa : phone_number,
        },
        dataType: 'json',
        beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success: function(result) {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          $('#div-verify').hide();
          $("#timer, #qr-code").html('');

          $('.message').show();
          $('.message').html(result.status);
          loadPhoneNumber();
        },
        error : function(xhr){
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $('#div-verify').hide();
          $("#timer, #qr-code").html('');

          alert('Sorry, unable to check if your phone verified, please try again later');
          console.log(xhr.responseText);
        }
      });
    }
    
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
      var phone_number = $(this).attr('data-phone');
      $("#phone_number").val(phone_number);
      getQRCode(phone_number);
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
