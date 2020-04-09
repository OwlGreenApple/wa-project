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

<!-- Modal Start to connect-->
<div class="modal fade" id="modal-start-connect" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content content-premiumid">
      <div class="modal-header header-premiumid">
        <h5 class="modal-title" id="modaltitle">
          Connect Your Phone
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <input type="hidden" name="id_phone_number" id="id_phone_number">

        <label><h4>Before connecting to our server <br>
        You must have "profile image" at your Whatsapp settings
        </h4></label>
        <br><br>
        <!--<span class="txt-mode"></span>-->
        <img src="{{url('assets/img/hint-setting.png')}}" class="img img-fluid">
        <br>
        
        <div class="col-12 mb-4" style="margin-top: 30px">
          <button class="btn btn-danger btn-block" data-dismiss="modal" id="button-start-connect">
            Start
          </button>
        </div>
        
        <div class="col-12 text-center mb-4">
          <a href="" class="" data-dismiss="modal">
            Cancel
          </a>  
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
      <li class="col-lg-4"><a id="tab1">Whatsapp Settings</a></li>
      <li class="col-lg-4"><a id="tab2">Account Settings</a></li>
  </ul>

  <!-- TABS CONTAINER -->
  <div class="tabs-content">
    <!-- TABS 1 -->
    <div class="tabs-container" id="tab1C">
      <div class="act-tel-settings">
        <div class="form-control message col-lg-9">
          <!-- logic here -->
        </div>

        <div class="row col-fix">
            <form class="wrapper add-contact col-lg-9 pad-fix" id="form-connect">
                <div class="form-group row col-fix">
                  <label class="col-sm-3 col-form-label">Phone Whatsapp :</label>
                  <div class="col-sm-9 row">
                   <!--  <div class="col-lg-3 row relativity">
                      <input id="code_country" name="code_country" class="form-control custom-select-campaign" value="+62" />
                      <span class="icon-carret-down-circle"></span>
                      <span class="error code_country"></span>
                    </div>
                    -->
                    <div class="col-sm-12">
                      <input type="text" id="phone" name="phone_number" class="form-control" />
                      <span class="error code_country"></span>
                      <span class="error phone_number"></span>
                    </div>
                    <!--<div>Please add avatar / image on your WA account.</div>-->
                    <div class="col-lg-12 pad-fix"><ul id="display_countries"><!-- Display country here... --></ul></div>
                  </div>
                </div>

                <div class="text-right">
                  <button type="button" id="button-connect" class="btn btn-custom" <?php if ($is_registered) { echo "disabled"; } ?>>Connect</button>
                </div>
            </form>

            <div class="col-lg-3 plan">
              <div>Current plan : <b>pro</b></div>
              <div>Valid Until 31 Dec 2020</div>
              <div><i>Buy More</i></div>
            </div>
        </div>

        <div class="wrapper verification" id="div-verify">
            <div class="form-group"><label class="col-sm-12 col-form-label">Scan this QR code from your <strong>Whatsapp Phone</strong></label></div>
            <div class="form-group row col-fix">
              <div class="col-lg-6"><div id="qr-code"></div></div>
              <div class="col-lg-6"><div id="timer"></div></h3></div>
            </div>

            <!-- <div class="text-right">
              <button type="button" id="button-verify" class="btn btn-custom">Submit</button>
            </div> -->
        </div>

        <div class="wrapper add-contact table-responsive" id="phone-table">
            <table class="table table-bordered mt-4">
              <thead class="bg-dashboard">
                <tr>
                  <th class="text-center">No</th>
                  <th class="text-center">Phone Whatsapp</th>
                  <th class="text-center">Status</th>
                  <th class="text-center">Delete</th>
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

<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
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
    //codeCountry();
    //putCallCode();

    $(".iti").addClass('w-100');
    $('#div-verify').hide();
    $('.message').hide();
    $('#phone-table').hide();
    <?php if ($is_registered) { ?>
      $('#phone-table').show();
    <?php } ?>
    $('#button-start-connect').click(function(){
      var phone_number = $("#phone_number").val();
      var code_country = $(".iti__selected-flag").attr('data-code');
      var dataphone = $("#form-connect").serializeArray();
      dataphone.push({name:'code_country', value:code_country});

      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'GET',
        url: "{{ url('connect-phone') }}",
        data: dataphone,
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
            $("#button-connect").prop('disabled',true);
            $("#phone_number").prop('disabled',true);
            $("#code_country").prop('disabled',true);
            // new system loadPhoneNumber();
            waitingTime();
            $(".error").hide();
          }

          if(data.status == "error") {
              $(".error").show();
              $(".phone_number").html(data.phone_number);
              $('.code_country').html(data.code_country);
          }

          if(data.message !== undefined){
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
    $('#button-connect').click(function(){
      $("#modal-start-connect").modal();
    });

   // Display Country

    function delay(callback, ms) {
      var timer = 0;
      return function() {
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }

    function codeCountry()
    { 
      $("input[name='code_country']").click(function(){$("input[name='code_country']").val('');});

      $("body").on('keyup focusin',"input[name='code_country']",delay(function(e){
          $("input[name='code_country']").removeAttr('update');
          var search = $(this).val();
          $.ajax({
            type : 'GET',
            url : '{{ url("countries") }}',
            data : {'search':search},
            dataType : 'html',
            success : function(result)
            {
              $("#display_countries").show();
              $("#display_countries").html(result);
            },
            error : function(xhr)
            {
              console.log(xhr.responseText);
            }
          });
      },500));

      $("input[name='code_country']").on('focusout',delay(function(e){
          var update = $(this).attr('update');
          if(update == undefined)
          {
            $("input[name='code_country']").val('+62');
            $("#display_countries").hide();
          }
      },200));
    }

    function putCallCode()
    {
      $("body").on("click",".calling_code",function(){
        var code = $(this).attr('data-call');
        $("input[name='code_country']").attr('update',1);
        $("input[name='code_country']").val(code);
        $("#display_countries").hide();
      });
    }
  // End Display Country
  var tm,flagtm;
  function waitingTime()
  {
      var scd = 0;
      var sc = 0;
      var min = 0;
      flagtm = false;
      tm = setInterval(function(){
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

          if( (scd == 180) || (scd == 233) || (scd == 287) || (scd == 329) || (scd == 359) )
          {
            // console.log("new system");
            if (flagtm == false ) {
              flagtm = true;
              getQRCode($("#code_country").val()+$("#phone_number").val());
            }
          }

          if(min == 6)
          {
              $("#secs").html('0'+0);
              clearInterval(tm);
          }

          sc++;
          scd++;
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
              /* new system $('.message').show();
              $('.message').append(result.phone_number);*/
              // getQRCode($("#code_country").val()+$("#phone_number").val());
              console.log(result);
            }
            else
            {
              $('#div-verify').show();
              $("#qr-code").html(result.data);
              clearInterval(tm);
              countDownTimer(phone_number);
            }
            flagtm = false;
            // new system loadPhoneNumber();
          },
          error : function(xhr,attr,throwable){
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
            alert('Sorry, unable to display QR-CODE, there is something wrong with our server, please try again later')
          }
        });

    }

    var timerCheckQrCode,flagTimerCheckQrCode;
    function countDownTimer(phone_number)
    {
      var sec = 25; //countdown timer
      var word = '<h3>Please scan qr-code before time\'s up :</h3>';
      flagTimerCheckQrCode=false;
      timerCheckQrCode = setInterval( function(){

          if( (sec == 20) || (sec == 15) || (sec == 10) || (sec == 1) ) {
            if (flagTimerCheckQrCode == false ) {
              flagTimerCheckQrCode = true;
              checkQRcode(phone_number);
            }
          }
          
          if(sec < 1){
            clearInterval(timerCheckQrCode);
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
          /* new system $('#loader').show();
          $('.div-loading').addClass('background-load');*/
        },
        success: function(result) {
          /* new system $('#loader').hide();
          $('.div-loading').removeClass('background-load');

          $('#div-verify').hide();
          $("#timer, #qr-code").html('');*/

          if (result.status!="none"){
            $('.message').show();
            $('.message').html(result.status);
          }  
          if (result.status=="Congratulations, your phone is connected"){
            $('#div-verify').hide();
            $("#timer, #qr-code").html('');
            $('#phone-table').show();
            loadPhoneNumber();
            clearInterval(timerCheckQrCode);
          }
          flagTimerCheckQrCode=false;
          /* new system loadPhoneNumber();*/
        },
        error : function(xhr){
          /* new system $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $('#div-verify').hide();
          $("#timer, #qr-code").html('');*/

          // alert('Sorry, unable to check if your phone verified, please try again later');
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
          $("#button-connect").prop('disabled',false);
          $("#phone_number").val("");
          // new system loadPhoneNumber();
        }
      });
    });


    /*$('body').on("click","#link-resend",function(){
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
      
    });*/

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
