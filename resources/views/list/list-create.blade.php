@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>LISTS</h2>
  </div>

  <div class="act-tel-dashboard-right">
     <a class="btn btn-custom">Create List</a>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
      <div class="input-group">
          <input type="text" class="form-control-lg col-lg-4 search-box" placeholder="Find a List By a name" >
          <span class="input-group-append">
            <div class="btn search-icon">
                <span class="icon-search"></span>
            </div>
          </span>
      </div>
  </div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="act-tel-tab">
      <div class="col-lg-12">
        <!-- tab 1 -->
        <div class="bg-dashboard cardlist row">
          <div class="col-lg-4 pad-fix col-card">
            <h5>TEST LIST 1</h5>
            <div>Link From : activele.com/xyz&nbsp;&nbsp;<span class="icon-copy"></span></div>
            <div>Create On : Jan 23, 2020</div>
          </div>

          <div class="col-lg-3 pad-fix cardnumber">
            <div class="big-number">+100</div>
            <div class="contact">New Contacts</div>
          </div> 

          <div class="col-lg-3 pad-fix cardnumber">
            <div class="big-number">+50</div>
            <div class="contact">Contacts</div>
          </div>

          <div class="col-lg-2 pad-fix col-button">
            <button type="button" class="btn btn-warning btn-sm"><span class="icon-eye"></span></button>
            <button type="button" class="btn btn-success btn-sm"><span class="icon-copy-text"></span></button>
            <button type="button" class="btn btn-danger btn-sm"><span class="icon-delete"></span></button>
          </div>
        </div> 
        <!-- tab 2 -->
        <div class="bg-dashboard cardlist row">
          <div class="col-lg-4 pad-fix col-card">
            <h5>TEST LIST 2</h5>
            <div>Link From : activele.com/xyz&nbsp;&nbsp;<span class="icon-copy"></span></div>
            <div>Create On : Jan 23, 2020</div>
          </div>

          <div class="col-lg-3 pad-fix cardnumber">
            <div class="big-number">+15</div>
            <div class="contact">New Contacts</div>
          </div> 

          <div class="col-lg-3 pad-fix cardnumber">
            <div class="big-number">+100</div>
            <div class="contact">Contacts</div>
          </div>

          <div class="col-lg-2 pad-fix col-button">
            <button type="button" class="btn btn-warning btn-sm"><span class="icon-eye"></span></button>
            <button type="button" class="btn btn-success btn-sm"><span class="icon-copy-text"></span></button>
            <button type="button" class="btn btn-danger btn-sm"><span class="icon-delete"></span></button>
          </div>
        </div> 
      </div>
  </div>
</div>
@endsection
