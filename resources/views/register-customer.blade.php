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
    <link href="{{ asset('/assets/css/waku.css') }}" rel="stylesheet">

    <?php echo $pixel;?>
     
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                   <!-- config('app.name', 'Laravel') -->
                </a>
            </div>
        </nav>

<main class="py-4">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
    
                <div class="col-md-12">
                    <?php echo $content;?>
                </div>

                <div class="card-body">
                    <div class="error_message"></div>
                    <form id="addcustomer">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                            <div class="col-md-7">
                                <input id="name" type="text" class="form-control" name="name" />
                                <span class="error name"></span>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('WA Number') }}</label>

                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-2">
                                        <input name="code_country" class="form-control" data-countryCode="ID" value="+62" readonly/>
                                        <span class="error code_country"></span>
                                    </div>
                                    <!-- end select -->    
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="wa_number" />
                                        <span class="error wa_number"></span>
                                    </div>
                                <!-- end row -->    
                                </div>
                                 <small>Tulis No HP dengan format: 8xxxxxxxx (tanpa angka 0 didepan), contoh: 812000333</small>
                            </div>
                        </div>

                        <div class="form-group row">
                        @foreach($additional as $row=>$val)
                            <label for="name" class="mb-2 col-md-4 col-form-label text-md-right">{{$row}}</label>

                            <div class="col-md-7 mb-2">
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
                            </div>
                        @endforeach
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6">
                                <input type="hidden" name="listname" value="{{$listname}}"/>
                                <input type="hidden" name="listid" value="{{$id}}"/>
                            </div>
                            <div class="col-md-6">
                                <span class="error error_list"></span>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <div id="submit">
                                    <button  type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
                        location.href= result.wa_link;
                        //getModal();
                        //setTimeout(function(){location.href= result.wa_link} , 1000);   
                        //clearField();
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
