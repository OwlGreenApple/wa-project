@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('broadcast')}}">Broadcast</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{route('reminder')}}">Reminder</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!- end navbar -->

<!-- 
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create Sender</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif 
                    @if (session('error'))
                        <div class="alert alert-warning" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                     <form method="POST" action="{{ route('addsender') }}">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">WA Number</label>

                            <div class="col-md-8">
                               <input type="text" class="form-control" name="wa_number" />
                               <small>Please do not use +62 62 or 0 but use 8xxxxxx instead</small>
                            </div>
                        </div>  
                        
                        <!-- submit button 
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Create Sender
                                </button>
                            </div>
                        </div>
                     </form>
                     <!-- end form 

                </div>
            </div>
        </div>
    </div>
<!-- end container 
</div>

-->

<!-- Modal Edit Dropdown -->
  <div class="modal fade child-modal" id="createNew" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2">
                    <input class="btn btn-warning btn-sm add-edit-option" type="button" value="Add Option" />
                </div>
               
                <label>Option List</label>
                <form id="optionform">
                    <div id="editoptions" class="form-group row">
                       <!-- display input here -->
                    </div> 

                    <input type="hidden" name="parent_id"/>
                    <input type="hidden" name="list_id"/>
                    <div class="form-group">
                       <button id="edp" class="btn btn-success btn-sm">Edit Dropdown</button>
                    </div>
                </form>
            </div>
        </div>
      </div>
      
    </div>
  </div>

<div class="container mb-2">
    <!-- Profile List -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                 @if (session('createnew'))
                    <div class="alert alert-success" role="alert">
                        {{ session('createnew') }}
                    </div>
                @endif
                <div class="card-header"><b>Create New</b></div>

                <div class="card-body">
                    <h4>Welcome To ActivWA</h4>
                    <a href="{{route('createdevice')}}" class="btn btn-primary">Create New</a>

                    <div id="img"><!-- wassenger barcode here --></div>
                </div>
            </div><!-- end card -->

            <div class="card">
                 @if (session('message'))
                    <div class="alert alert-success" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                <div class="card-header"><b>Profile User</b></div>

                <div class="card-body">
                     <form method="POST" action="{{ route('updateuser') }}">
                        @csrf
                        <!-- name -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{$user->name}}" />
                            </div>
                        </div>  
                       
                        <!-- Password -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Change Password </label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password" />
                            </div>
                        </div> 
                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Data
                                </button>
                            </div>
                        </div>
                     </form>
                     <!-- end form -->

                </div>
            </div><!-- end card -->

        </div>
    </div>
<!-- end container -->
</div>  

<script type="text/javascript">
    $(document).ready(function(){
        getScanBarcode();
    });

    function getScanBarcode()
    {

        $("#img").text('Loading....');
        $.ajax({
            type : 'GET',
            url : '{{route("scan")}}',
            dataType : 'html',
            success : function(result)
            {
                $("#img").html(result);
            }
        })


        /*var settings = {
          "async": true,
          "crossDomain": true,
          "url": "https://api.wassenger.com/v1/devices/5d6e15906de1a4001c90a0f4/scan?force=true"+'?callback=?',
          "method": "GET",
          "headers": {
            'Access-Control-Allow-Origin': '*'
          },
          data : {
            "token": "717c449cac6613abd70349cbd889b4955523292e7a45c49ebb2880b9b77e944d44f467389e75a080",
          },
          contentType : 'application/javascript',
          dataType : 'jsonp',
          jsonpCallback: "localJsonpCallback"
        }

        function localJsonpCallback(json)
        {
            console.log(json);
        }

        $.ajax(settings).done(function (response) {
           console.log(response);
        });

        */
    }
</script>
@endsection
