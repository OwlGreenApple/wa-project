@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a href="{{route('home')}}" class="nav-link">Back Home</a>
                </li>
                <li class="nav-item">
                    <a href="{{route('broadcast')}}" class="nav-link">See Broadcast List</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!-- end navbar -->

<div class="container">
    <!-- add list-->
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Create BroadCast</b></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                         </div>   
                    @endif 

                    @if (session('status_error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('status_error') }}
                         </div>   
                    @endif

                     <form method="POST" action="{{ route('createbroadcast') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Lists Option</label>
                            <div class="col-md-6">
                                @foreach($data as $row)
                                <div class="form-check">
                                  <input class="form-check-input" name="id[]" type="checkbox" value="{{$row->id}}">
                                  <label class="form-check-label" for="{{$row->id}}">
                                    {{$row->name}}
                                  </label>
                                </div>
                                 @endforeach
                                <!-- end check box -->
                            </div>
                        </div> 

                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Message</label>
                            <div class="col-md-6">
                                <textarea id="divInput-description-post" class="form-control" name="message"></textarea>
                            </div>
                        </div> 

                        <!-- submit button -->
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                   Create Broadcast
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

<!-- give emoji -->
 <script type="text/javascript">
    $("#divInput-description-post").emojioneArea({
        pickerPosition: "top",
        mainPathFolder : "{{url('')}}",
    });
</script>

@endsection
