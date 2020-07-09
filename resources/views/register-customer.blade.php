<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title></title>
		<!-- Icon -->
		<link rel='shortcut icon' type='image/png' href="{{ asset('assets/img/favicon.png') }}">
    <!-- Scripts -->
    <script src="{{ asset('/assets/js/jquery-3.2.1.min.js') }}"></script>
    <script src="{{ asset('/assets/js/app.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="{{ asset('/assets/css/nunito.css') }}" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/subscribe.css') }}" rel="stylesheet" />

     <!-- Intl Dialing Code -->
    <link href="{{ asset('/assets/intl-tel-input/css/intlTelInput.min.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('/assets/intl-tel-input/js/intlTelInput.js') }}"></script> 

    <!-- Icomoon -->
    <link href="{{ asset('/assets/icomoon/icomoon.css') }}" rel="stylesheet" />

    {!! $pixel !!}

    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>"></script>
    
</head>

<body class="act-tel-subscribe-page">

<!--Loading Bar-->
<div class="div-loading">
  <div id="loader" style="display: none;"></div>  
</div> 

<div id="app">
  <!--<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
      <div class="container">
          <a class="navbar-brand" href="{{ url('/') }}">
             <!-- config('app.name', 'Laravel') 
          </a>
      </div>
  </nav>-->

  <main class="p-5">

    <div class="container">
        <div class="row justify-content-center">
          
           <div class="act-tel-subscribe col-lg-9">
              <div class="wrapper act-tel-subscribe-img">
                  {!! $content !!}
              </div>

              <div class="wrapper">
                <div id="message_id">
                <span class="error main"></span>

                <form class="add-contact" id="addcustomer">
                    <div class="form-group">
                      <label>{{ $label_name }}*</label>
                      <input type="text" name="subscribername" class="form-control" />
                      <span class="error name"></span>
                    </div>

                    @if($checkbox_lastname > 0)
                    <div class="form-group">
                      <label>{{ $label_last_name }}*</label>
                      <input type="text" name="last_name" class="form-control" />
                      <span class="error last_name"></span>
                    </div> 
                    @endif

                    <div class="prep1">
                      <div class="form-group">
                          <label>{{ $label_phone }}*</label>
                          <div class="col-sm-12 row">
                            <input class="form-control" id="phone" name="phone_number" type="tel">
                            <span class="error code_country"></span>
                            <span class="error phone"></span>
                          </div>
                      </div>

                      <!-- <div class="form-group">
                          <label>{{ $label_phone }}*</label>
                          <div class="col-sm-12 row">
                            <div class="col-lg-3 row relativity">
                              <input name="code_country" class="form-control custom-select-campaign" value="+62" autocomplete="off" />
                              <span class="icon-carret-down-circle"></span>
                              <span class="error code_country"></span>
                            </div>

                            <div class="col-sm-9">
                              <input type="text" id="phone_number" name="phone_number" class="form-control" />
                              <span class="error phone"></span>
                            </div>
                            <div class="col-lg-12 pad-fix"><ul id="display_countries"><!-- Display country here... </ul></div>
                          </div>
                      </div> -->
                    </div>

                    @if($checkbox_email > 0)
                    <div class="form-group">
                      <label>{{ $label_email }}*</label>
                      <input type="email" name="email" class="form-control" />
                      <span class="error email"></span>
                    </div> 
                    @endif

                    @if(count($additional) > 0)
                      @foreach($additional as $is_optional=>$row)
                          @foreach($row as $name=>$val)
                          <div class="form-group">
                              @if($is_optional > 0)
                                <label>{{$name}}*</label>
                              @else
                                <label>{{$name}}</label>
                              @endif
                           
                            @foreach($val as $key=>$col)
                                @if($key == 0)
                                   <input type="text" class="form-control" name="data[{{$name}}]" />
                                @else
                                  <select class="form-control" name="data[{{$name}}]">
                                      @foreach($col as $opt)
                                          <option value="{{$opt}}">{{$opt}}</option>
                                      @endforeach
                                  </select>
                                @endif
                            @endforeach
                             <span class="error {{$name}}"></span>
                            </div>
                        @endforeach
                        <!-- -->
                      @endforeach
                    @endif

                    <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                
                    <div class="text-left">
                      <button type="submit" class="btn btn-custom btn-lg">{{ $btn_message }}</button>
                    </div>

                    <span class="error captcha"></span>
                </form>
              <!-- END MESSAGE_ID -->
              </div>

              <div id="button_add_appointment"><a class="btn btn-custom" href="{{ $link_add_customer }}">Register Another</a></div>

              <div class="text-left marketing">
                <a href="https://activrespon.com" target="_blank">
                  <div>Marketing by</div>
                  <div><img src="{{asset('assets/img/marketing-logo.png')}}"/></div>
                </a>
              </div>
            </div>
            <!-- end wrapper -->
          </div>

        </div>
    </div>

    <!-- Modal -->
    <div id="myModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <!-- <div class="modal-header">
            <h4 class="modal-title">Thank You</h4>
          </div> -->
          <div class="modal-body text-center">
            Your data has stored!
          </div>
        </div>

      </div>
    </div>

  </main>
 </div>

<script src="{{ asset('/assets/intl-tel-input/callback.js') }}" type="text/javascript"></script>
<script type="text/javascript">
  $(document).ready(function() {
      //choose();
      grecaptcha.ready(function() {
        grecaptcha.execute("<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>", {action: 'contact_form'}).then(function(token) {
            $('#recaptchaResponse').val(token);
            // console.log(token);
        });
      });
      saveSubscriber();
      /*//codeCountry()
      putCallCode();*/
      fixWidthPhoneInput();
			<?php if(session('message')) { ?>
			alert("<?php echo session('message'); ?>");
			<?php }?>
  });

  function fixWidthPhoneInput()
  {
    $(".iti").addClass('w-100');
  }

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

  function saveSubscriber(){
      $("#button_add_appointment").hide();
      $("#addcustomer").submit(function(e){
          e.preventDefault();
          var code_country = $(".iti__selected-flag").attr('data-code');
          var data_country = $(".iti__selected-flag").attr('data-country');
          var data = $(this).serializeArray();
      
          data.push(
            {name:'code_country', value:code_country},
            {name:'data_country',value:data_country},
            {name:'listname',value:'{{ $listname }}'},
            {name:'listid',value:'{{ $id }}'},
          );

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
                  $("#message_id").html(result.message);
                  if(result.is_appointment == 1)
                  {
                    $("#button_add_appointment").show();
                  }
                  /*  $(".modal-body > p").text(result.message);
                    $("#myModal").modal();*/
                    // setTimeout(function(){$("#myModal").modal('hide')} , 1500);   
                    // clearField();
                } else {
                    $(".error").html('');
                    $(".error").fadeIn('slow');
                    $(".name").text(result.name);
                    $(".last_name").text(result.last_name);
                    $(".email").text(result.email);
                    $(".phone").text(result.phone);
                    $(".code_country").text(result.code_country);
                    $(".captcha").text(result.captcha);
                    $(".error_list").text(result.list);
                    $(".error_list").text(result.list);
                    $(".main").html(result.main);

                    if(result.message !== undefined){
                         $(".error_message").html('<div class="alert alert-danger text-center">'+result.message+'</div>');
                    }
                    $.each(result.data, function(key, value) {
                        $("."+key).text(value);
                    })

                    $(".error").delay(5000).fadeOut(5000);
                }
              },
              error : function(xhr)
              {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                console.log(xhr.responseText);
              }
          });
          /*end ajax*/
      });
  }

  /* Clear / Empty fields after ajax reach success */
  function clearField(){
      $("input:not([name='listid'],[name='listname'])").val('');
      $(".error").html('');
  }
  
  /*function choose(){
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
  }*/
</script>

</body>
</html>
