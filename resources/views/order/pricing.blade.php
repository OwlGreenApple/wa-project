@extends('layouts.app')

@section('content')
<link href="{{ asset('/assets/css/pricing.css') }}" rel="stylesheet" />

<!--Plugin CSS file with desired skin-->
<link rel="stylesheet" href="{{ asset('/assets/ion.rangeSlider-master/css/ion.rangeSlider.min.css') }}"/>
<!--Plugin JavaScript file-->
<script src="{{ asset('/assets/ion.rangeSlider-master/js/ion.rangeSlider.min.js') }}"></script>

<!-- TOP SECTION -->
<section class="page-title">
  <div class="container">
    <div class="row">
      <div class="col-12">
      <!--
        <h1 class="">Omnilinkz <strong>Pricing Plans</strong></h1>
        -->
      </div>
    </div>
  </div>
</section>

<div class="container main-pricing">
  <div class="row h-100">
      <!--
      <div class="card card-block w-25 mx-auto">I am Groot.</div>
      -->
      <h1 class="col-12">
        Choose Your Package
      </h1>
      <div class="col-1 ">
      </div>
      <div class="col-6 row price-content">
        <h3>
          How many contacts you have ? 
        </h3>
        <div class="col-12 div-range-slider">
          <input type="text" class="js-range-slider" name="my_range" value="" />
        </div>
        <div class="col-4 ml-0 mr-0 box-pricing p-2 selected">
          <div class="box-in">
            <div class="div-header w-100 p-3">
              <h5>Super Value</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>1 Year </span>
              <span>&nbsp</span>
              <span>IDR 2,970,000</span>
            </div>
          </div>            
        </div>            
        <div class="col-4 ml-0 mr-0 box-pricing p-2 ">
          <div class="box-in">
            <div class="div-header w-100 p-3">
              <h5>Best Seller</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>3 Months </span>
              <span>&nbsp</span>
              <span>IDR 759,000</span>
            </div>
          </div>            
        </div>    
        <div class="col-4 ml-0 mr-0 box-pricing p-2 ">
          <div class="box-in">
            <div class="div-header w-100 p-3">
              <h5>Basic</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>1 Month </span>
              <span>&nbsp</span>
              <span>IDR 275,000</span>
            </div>
          </div>            
        </div>    
        
      </div>
      <div class="col-1 ">
      </div>
      <div class="col-4 detail-description">
        <button class="btn btn-success button-bottom-buynow">Buy Now<i class="fa fa-chevron-right" aria-hidden="true"></i></button>
        <h3>
          25.000 Kontak / Tahun
        </h3>
        <button type="button" class="btn btn-success form-control">IDR 2.970.000</button>
        <ul>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Broadcast Schedule</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Broadcast Now</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Message Automation</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Auto reply / responder</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Subscribtion Form</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Statistic</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Download kontak list</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>FB Pixel / Google Retargetting</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Event Reminder</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Appointment Reminder</span>
          </li>
        </ul>
      </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    var custom_values = [1000, 2500, 5000, 7500, 10000, 15000, 20000, 25000, 50000, 75000];
    $(".js-range-slider").ionRangeSlider({
        grid: true,
        skin: "round",
        values: custom_values
    });
  });
</script>
@endsection
 