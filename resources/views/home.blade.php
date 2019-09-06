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
<!-- end navbar -->

<div class="container mb-2">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create List</b></div>

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

                     <form method="POST" action="{{ route('addlist') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-3 col-form-label text-md-right">Name List</label>

                            <div class="col-md-8">
                                <input id="name" type="text" class="form-control" name="name" />
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">No WA</label>

                            <div class="col-md-8">
                                <input type="text" class="form-control" name="wa_number" />
                            </div>
                        </div>  
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label text-md-right">API Key</label>

                            <div class="col-md-8">
                                <input type="text" class="form-control" name="api_key" />
                            </div>
                        </div>  
                        <div class="form-group">
                            <div class="col-md-12">
                                 <textarea name="editor1" id="editor1" rows="10" cols="80"></textarea>
                            </div>
                        </div> 
                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Add List
                                </button>
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

<div class="container mb-2">
    <!-- Profile List-->
    <div class="row justify-content-center">
        <div class="col-md-8">
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
                        <!-- wa number -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">WA Number</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="wa_number"  value="{{$user->wa_number}}" />
                            </div>
                        </div>  
                        <!-- API key -->
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">API Key</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="api_key"  value="{{$user->api_key}}" />
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
            </div>
        </div>
    </div>
<!-- end container -->   
</div>
@endsection
