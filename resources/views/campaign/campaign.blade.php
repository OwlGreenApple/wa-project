@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2 class="campaign">Campaigns</h2>
  </div>

  <div class="act-tel-dashboard-right">
     <a href="{{url('create-campaign')}}" class="btn btn-custom">Create Campaign</a>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container mt-2">
  <div class="act-tel-tab">
    <div class="row">
      <div class="input-group col-lg-4">
          <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find a campaign by a name or a type" >
          <span class="input-group-append">
            <div class="btn search-icon">
                <span class="icon-search"></span>
            </div>
          </span>
      </div> 

      <div class="col-lg-6"></div>

      <div class="input-group col-lg-2">
         <select class="custom-select-campaign form-control col-lg-10 relativity">
            <option>Event</option>
            <option>Broadcast</option>
            <option>Auto Responder</option>
         </select>
         <span class="icon-triangular"></span>
      </div>

      <div class="clearfix"></div>

    </div>
  </div>
</div>

<!-- NUMBER -->
<div class="container">
  <div class="act-tel-tab">
      <div class="col-lg-12">
        <!-- tab 1 -->
        <div class="bg-dashboard campaign row">
          <div class="col-lg-4 pad-fix col-card">
            <h5>Test Campaign</h5>
            <div class="notes">
              <div>Type Campaign : <color>Event</color></div>
              <div>Date Send : <b>Jan 28, 2020</b></div>
              <div>List : Test List 1</div>
            </div>
            <div class="created">
              Create On : Jan 23, 2020
            </div>
          </div>

          <div class="col-lg-5 pad-fix mt-4">
            <div class="row">
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100</div>
                  <div class="contact">Message</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100</div>
                  <div class="contact">Send</div>
                </div> 
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100</div>
                  <div class="contact">Opened</div>
                </div>
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100%</div>
                  <div class="contact">Open Rate</div>
                </div>
            </div>  
          </div>

          <div class="col-lg-3 pad-fix col-button">
            <a href="{{url('report-reminder')}}" class="btn btn-warning btn-sm"><span class="icon-eye"></span></a>
            <button type="button" class="btn btn-success btn-sm"><span class="icon-copy-text"></span></button>
            <button type="button" class="btn btn-danger btn-sm"><span class="icon-delete"></span></button>
            <div><a href="{{url('add-reminder')}}" class="btn btn-custom">Add Message</a></div>
          </div>
        </div> 

        <!-- tab 2 -->

        <div class="bg-dashboard campaign row">
          <div class="col-lg-4 pad-fix col-card">
            <h5>Campaign 1</h5>
            <div class="notes">
              <div>Type Campaign : <color><span class="gr">Broadcast</span></color></div>
              <div>Date Send : <b>Jan 28, 2020</b></div>
              <div>List : Test List 1</div>
            </div>
            <div class="created">
              Create On : Jan 23, 2020
            </div>
          </div>

          <div class="col-lg-5 pad-fix mt-4">
            <div class="row">
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">10</div>
                  <div class="contact">Message</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">10</div>
                  <div class="contact">Send</div>
                </div> 
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">9</div>
                  <div class="contact">Opened</div>
                </div>
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">9%</div>
                  <div class="contact">Open Rate</div>
                </div>
            </div>  
          </div>

          <div class="col-lg-3 pad-fix col-button">
            <button type="button" class="btn btn-warning btn-sm"><span class="icon-eye"></span></button>
            <button type="button" class="btn btn-success btn-sm"><span class="icon-copy-text"></span></button>
            <button type="button" class="btn btn-danger btn-sm"><span class="icon-delete"></span></button>
          </div>
        </div> 

        <!-- tab 3 -->

        <div class="bg-dashboard campaign row mb-0">
          <div class="col-lg-4 pad-fix col-card">
            <h5>Campaign 2</h5>
            <div class="notes">
              <div>Type Campaign : <color><span class="og">Auto Responder</span></color></div>
              <div>Date Send : <b>Jan 28, 2020</b></div>
              <div>List : Test List 1</div>
            </div>
            <div class="created">
              Create On : Jan 23, 2020
            </div>
          </div>

          <div class="col-lg-5 pad-fix mt-4">
            <div class="row">
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100</div>
                  <div class="contact">Message</div>
                </div>  
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">100</div>
                  <div class="contact">Send</div>
                </div> 
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">90</div>
                  <div class="contact">Opened</div>
                </div>
                <div class="col-lg-3 pad-fix cardnumber">
                  <div class="big-number">90%</div>
                  <div class="contact">Open Rate</div>
                </div>
            </div>  
          </div>

          <div class="col-lg-3 pad-fix col-button">
              <button type="button" class="btn btn-warning btn-sm"><span class="icon-eye"></span></button>
              <button type="button" class="btn btn-success btn-sm"><span class="icon-copy-text"></span></button>
              <button type="button" class="btn btn-danger btn-sm"><span class="icon-delete"></span></button>
              <div>
                <button type="button" class="btn btn-custom">Add Message</button>
              </div>
          </div>
        </div> 
        
        <!-- end tab -->
  </div>
</div>
@endsection
