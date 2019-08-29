@extends('layouts.app')

@section('content')
<div class="container">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Broad Cast</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                     <form method="POST" action="{{ route('addlist') }}">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Message</label>
                            <div class="col-md-6">
                                <textarea class="form-control" name="message"></textarea>
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">List</label>
                            <div class="col-md-6">
                                <select class="form-control" name="list">
                                    @if(is_null($data))
                                        <option>No Data</option>
                                    @else
                                        @foreach($data as $row)
                                            <option value="{{$row->id}}">{{$row->name}}</option>
                                        @endforeach
                                    @endif    
                                </select>
                            </div>
                        </div>

                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Broadcast
                                </button>
                            </div>
                        </div>
                     </form>
                     <!-- end form -->

                     <div class="form-group row">
                        <a href="{{route('home')}}" class="btn btn-default btn-sm">Back Home</a> 
                    </div>
                     
                </div>
            </div>
        </div>
    </div>
<!-- end container -->   
</div>
@endsection
