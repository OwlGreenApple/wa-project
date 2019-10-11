@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('home')}}">Broadcast</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!- end navbar -->

<!-- -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create New Device</b></div>

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

                     <form method="POST" action="">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">Device Name</label>

                            <div class="col-md-8">
                               <input type="text" class="form-control" name="device_name" />
                            </div>
                        </div>  

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">WA Number</label>

                            <div class="col-md-8">
                               <input type="text" class="form-control" name="wa_number" />
                               <small>Please do not use +62 62 or 0 but use 8xxxxxx instead</small>
                            </div>
                        </div>  
                        
                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <a href="{{route('devicepackage')}}" class="btn btn-primary">
                                    Create Sender
                                </a>
                            </div>
                        </div>
                     </form>
                     <!-- end form -->

                </div>
            </div>
        </div>
    </div>
<!-- end container -->
</div>
@endsection
