@extends('layouts.app')

@section('content')

<!-- navbar 
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
 end navbar -->

<!--
<div class="container mb-2">
    <!-- add lis
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

<div class="container mb-2">
    <!-- Profile List
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
                        <!-- name 
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>

                            <div class="col-md-6">
                                <input type="text" class="form-control" name="name" value="{{$user->name}}" />
                            </div>
                        </div>  
                       
                        <!-- Password 
                        <div class="form-group row">
                            <label for="name" class="col-md-4 col-form-label text-md-right">Change Password </label>

                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password" />
                            </div>
                        </div> 
                        <!-- submit button 
                         <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Update Data
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

<script type="text/javascript">
    $(document).ready(function(){
        //addWACol();
        //delWACol();
    });

    function addWACol(){
        $("body").on("click",".addfield",function(){
            var pos = $(".wa_number_box").length;
            var box_field = '<input type="text" class="pos-'+pos+' form-control float-left col-sm-10 mb-2 wa_number_box" name="wa_number[]" /><a class="btn btn-warning float-left del" id="pos-'+pos+'">Delete</a><div class="clearfix"></div>';
            $(".item").append(box_field);
        });
    }

     function delWACol(){
        $("body").on("click",".del",function(){
           var id = $(this).attr('id');
           $("."+id).remove();
           $("#"+id).remove();
        });
    }
</script>

@endsection
