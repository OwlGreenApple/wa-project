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
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('/assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/css/main.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/subscribe.css') }}" rel="stylesheet" />

    <!--!! $pixel !!-->
     
</head>

<body class="bg-dashboard">

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
            <!--!! $content !!-->

           <div class="act-tel-subscribe col-lg-9">
              <div class="wrapper">
                <form class="add-contact">
                    <div class="form-group">
                      <label>Name*</label>
                      <input type="text" class="form-control" placeholder="Input Your Name" >
                    </div>

                    <div class="form-group">
                      <label>Handphone*</label>
                      <input type="text" class="form-control" placeholder="6280000" />
                      <i>*) format phone : 6280000</i>
                    </div>

                    <div class="form-group">
                      <label>Username Telegram*</label>
                      <input type="text" class="form-control" placeholder="Input Your Telegram Username" />
                    </div>

                    <div class="form-group">
                      <label>Email*</label>
                      <input type="text" class="form-control" placeholder="Input Your Email" />
                    </div> 

                    <div class="form-group">
                      <label>Custom Field</label>
                      <input type="text" class="form-control" placeholder="Input Your Custom Field" />
                    </div>

                    <div class="form-group">
                      <label>Please verify your request</label>
                    </div>

                    <div class="text-left">
                      <button type="submit" class="btn btn-custom btn-lg">Submit</button>
                    </div>
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

    <script type="text/javascript">

        $(document).ready(function() {
             addCustomer();
        });

        function addCustomer(){
            $("#addcustomer").submit(function(e){
                e.preventDefault();
                var data = $(this).serialize();
                $("#submit").html('<img src="{{asset('assets/css/loading.gif')}}"/>');
                 $.ajaxSetup({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  }
                });
                $.ajax({
                    type : "POST",
                    url : "{{ route('addcustomer') }}",
                    data : data,
                    success : function(result){
                        $("#submit").html('<button type="submit" class="btn btn-primary">Register</button>');
                        if(result.success == true){
                            $(".modal-body > p").text(result.message);
                            alert('Your data has stored!');
                            //location.href= result.wa_link;
                            //getModal();
                            //setTimeout(function(){location.href= result.wa_link} , 1000);   
                            clearField();
                        } else {
                            $(".name").text(result.name);
                            $(".wa_number").text(result.wa_number);
                            $(".code_country").text(result.code_country);
                            $(".error_list").text(result.list);

                            if(result.message !== undefined){
                                 $(".error_message").html('<div class="alert alert-danger text-center">'+result.message+'</div>');
                            }
                            $.each(result.data, function(key, value) {
                                $("."+key).text(value);
                            })
                        }
                    }
                });
                /*end ajax*/
            });
        }

        /* Display modal when customer has finished registering */
        function getModal(){
            $("#myModal").modal()
        }

        /* Clear / Empty fields after ajax reach success */
        function clearField(){
            $("input[name='name'],input[name='wa_number']").val('');
            $(".error").html('');
        }
    </script>

  </main>
 </div>

</body>
</html>
