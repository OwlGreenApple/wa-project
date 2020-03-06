<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title></title>

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

  <main class="py-4">

    <div class="container">
        <div class="row justify-content-center">
          
           <div class="act-tel-subscribe col-lg-9">
              <div class="wrapper act-tel-subscribe-img">
                  {!! $content !!}
              </div>

              <div class="wrapper">
                <span class="error main"></span>

                <form class="add-contact" id="addcustomer">
                    <div class="form-group">
                      <label>Name*</label>
                      <input type="text" name="subscribername" class="form-control" placeholder="Input Your Name" >
                      <span class="error name"></span>
                    </div>

                    <div class="prep1">
                      <div class="form-group">
                          <label>Phone Number*</label>
                          <input type="text" name="phone_number" class="form-control" placeholder="Input your phone number"/>
                          <span class="error phone"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label>Email*</label>
                      <input type="email" name="email" class="form-control" placeholder="Input Your Email" />
                      <span class="error email"></span>
                    </div> 

                    @if(count($additional) > 0)
                      @foreach($additional as $row=>$val)
                        <div class="form-group">
                            <label>{{$row}}</label>

                            @foreach($val as $key=>$col)
                                @if($key == 0)
                                     <input type="text" class="form-control" name="data[{{$row}}]" />
                                @else
                                    <select name="data[{{$row}}]" class="form-control">
                                        @foreach($col as $opt)
                                            <option value="{{$opt}}">{{$opt}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            @endforeach
                            <span class="error {{$row}}"></span>
                       </div>
                      @endforeach
                    @endif

                    <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
                    <input type="hidden" name="listname" value="{{$listname}}">
                    <input type="hidden" name="listid" value="{{$id}}">

                    <div class="text-left">
                      <button type="submit" class="btn btn-custom btn-lg">Submit</button>
                    </div>

                    <span class="error captcha"></span>
                </form>

              <div class="text-left marketing">
                  <div>Marketing by</div>
                  <div><img src="{{asset('assets/img/marketing-logo.png')}}"/></div>
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
          <div class="modal-header">
            <h4 class="modal-title">Thank You</h4>
          </div>
          <div class="modal-body">
            <p><!-- message here --></p>
          </div>
        </div>

      </div>
    </div>

  </main>
 </div>

  <script type="text/javascript">

        $(document).ready(function() {
            //choose();
            grecaptcha.ready(function() {
              grecaptcha.execute("<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>", {action: 'contact_form'}).then(function(token) {
                  $('#recaptchaResponse').val(token);
                  console.log(token);
              });
            });
            saveSubscriber();
        });

        function saveSubscriber(){
            $("#addcustomer").submit(function(e){
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
                          $(".modal-body > p").text(result.message);
                          alert('Your data has stored!');
                          //getModal();
                          //setTimeout(function(){location.href= result.wa_link} , 1000);   
                          // clearField();
                      } else {
                          $(".error").fadeIn('fast');
                          $(".name").text(result.name);
                          $(".main").text(result.main);
                          $(".email").text(result.email);
                          $(".phone").text(result.phone);
                          $(".phone").text(result.usertel);
                          $(".captcha").text(result.captcha);
                          $(".error_list").text(result.list);

                          if(result.message !== undefined){
                               $(".error_message").html('<div class="alert alert-danger text-center">'+result.message+'</div>');
                          }
                          $.each(result.data, function(key, value) {
                              $("."+key).text(value);
                          })

                          $(".error").delay(2000).fadeOut(5000);
                      }
                    }
                });
                /*end ajax*/
            });
        }

        /* Display modal when customer has finished registering */
        function getModal(){
            $("#myModal").modal();
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
