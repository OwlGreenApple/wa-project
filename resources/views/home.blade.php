@extends('layouts.app')

@section('content')
<div class="container">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Add List</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                     <form method="POST" action="{{ route('addlist') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name List</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" />
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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

                     <!-- User List table -->
                      <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                               <a class="btn btn-warning" href="{{route('userlist')}}">See List</a>
                            </div>
                      </div>
                     
                     <a href="{{route('broadcast')}}" class="btn btn-info btn-sm">Bradcast</a>
                     <a href="{{route('reminder')}}" class="btn btn-success btn-sm">Reminder</a>

                </div>
            </div>
        </div>
    </div>
<!-- end container -->   
</div>
@endsection
