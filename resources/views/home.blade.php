@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>DASHBOARD</h2>
  </div>

  <div class="act-tel-dashboard-right">
      <div>Current plan : <b>pro</b></div>
      <div>Valid Until 31 Dec 2020</div>
      <div><i>Buy More</i></div>
  </div>
  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="row justify-content-center act-tel-number">
    <div class="col-center col-lg-3 act-tel-number-left bg-dashboard">10 <br/><small>Lists</small></div>
    <div class="col-center col-lg-3 act-tel-number-right bg-dashboard">5 <br/><small>Campaigns</small></div>
  </div>
</div>

<!-- LIST -->
<div class="container act-tel-dashboard">
  <div class="act-tel-list-board">
    <div class="act-tel-list-left">List</div>
    <div class="act-tel-list-right"><span class="icon-carret-down-circle"></span></div>
    <div class="clearfix"></div>
  </div>

  <div class="act-tel-list bg-dashboard row col-fix">
   
      <div class="col-md-5 act-tel-list-left-col">
        <div class="big-number">+1000</div>
        <div class="contact">New Contact Lists (7 days)</div>

        <div class="row mt-3">
          <div class="col-lg-5 col-number"><b>1000</b> <div class="ml-1 act-tel-list-m-list">List</div></div>
          <div class="col-lg-7 col-number"><b>1563</b> <div class="ml-1 act-tel-list-m-list">Total Contacts</div></div>
        </div>

        <div class="mt-2">
          <button type="button" class="btn-custom">View List</button>
        </div>
      </div>

      <div class="col-md-7 act-tel-bg-list">
        <!-- sec column -->
      </div>
    </div>

</div>

<!-- CAMPAIGNS -->
<div class="container act-tel-dashboard">
  <div class="act-tel-list-board">
    <div class="act-tel-list-left">Campaigns</div>
    <div class="act-tel-list-right"><span class="icon-carret-down-circle"></span></div>
    <div class="clearfix"></div>
  </div>

  <div class="act-tel-dashboard view-campaign bg-dashboard row col-fix">
   
      <div class="col-md-5 act-tel-list-left-col">

        <div class="row mt-3">
          <div class="col-lg-6 col-number"><b>1000</b> <div class="ml-1 act-tel-list-m-list">Total Message</div></div>
          <div class="col-lg-6 col-number"><b>1563</b> <div class="ml-1 act-tel-list-m-list">Total Send</div></div>
        </div>

        <div class="row mt-3">
          <div class="col-lg-6 col-number"><b>1000</b> <div class="ml-1 act-tel-list-m-list">Total Opened</div></div>
          <div class="col-lg-6 col-number"><b>100%</b> <div class="ml-1 act-tel-list-m-list">Total Open Rate</div></div>
        </div>

        <div class="mt-2">
          <button type="button" class="btn-custom">View Campaigns</button>
        </div>
      </div>

      <div class="col-md-7 act-tel-bg-list">
        <!-- sec column -->
      </div>
    </div>

</div>

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
-->
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

<!-- Modal Edit Dropdown
  <div class="modal fade child-modal" id="createNew" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content
      <div class="modal-content">
        <div class="modal-body">
            <div class="form-group">
                 <div class="mb-2">
                    <input class="btn btn-warning btn-sm add-edit-option" type="button" value="Add Option" />
                </div>
               
                <label>Option List</label>
                <form id="optionform">
                    <div id="editoptions" class="form-group row">
                       <!-- display input here 
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
  </div>-->

<!-- 
<div class="container mb-2">
    Profile List 
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
                    <a href="{{route('registerdevice')}}" class="btn btn-primary">Create New</a>
                </div>
            </div><!-- end card

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
            </div><!-- end card 

        </div>
    </div>
<!-- end container
</div>  -->
@endsection
