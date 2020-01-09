@extends('layouts.admin')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('home')}}">Home</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!- end navbar -->

<div class="container mb-2">
    <!-- Profile List -->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Total deliver message per : -->
            <div class="col-md-12"><label>Total deliver message per :</label></div>
            <div class="col-md-12">
                <form class="form-inline">
                  <div class="form-group mb-2">
                    <select class="form-control">
                      @for($x=1;$x<=10;$x++)
                        <option value="{{$x}}">{{$x}}</option>
                      @endfor
                    </select>
                  </div>
                  <div class="form-group mx-sm-3 mb-2">
                    <select class="form-control">
                      @for($x=1;$x<=10;$x++)
                        <option value="{{$x}}">{{$x}}</option>
                      @endfor
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary mb-2">Set Settings</button>
                </form>
            </div>
           
            <!-- Delay message per : -->
            <div class="col-md-12"><label>Delay send message per :</label></div>
            <div class="col-md-12">
                <form class="form-inline">
                  <div class="form-group mb-2">
                    <select class="form-control">
                      @for($x=1;$x<=10;$x++)
                        <option value="{{$x}}">{{$x}}</option>
                      @endfor
                    </select>
                  </div>
                  <div class="form-group mx-sm-3 mb-2">
                    <select class="form-control">
                      @for($x=1;$x<=10;$x++)
                        <option value="{{$x}}">{{$x}}</option>
                      @endfor
                    </select>
                  </div>
                  <button type="submit" class="btn btn-primary mb-2">Set Delay</button>
                </form>
            </div>
        </div>
    </div>
<!-- end container -->
</div>  

@endsection