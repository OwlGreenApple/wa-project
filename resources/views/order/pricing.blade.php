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
      <div class="col-1 ">
      </div>
      <div class="col-6 row">
        <h3>
          How many contacts you have ? 
        </h3>
        <div class="col-12 div-range-slider">
          <input type="text" class="js-range-slider" name="my_range" value="" />
        </div>
        <div class="col-4 ml-0 mr-0 box-pricing p-3 ">
          <div class="box-in">
            <div class="div-header w-100 w-100">
              Super Value
            </div>
            <div class="div-content w-100 w-100 p-3">
              asdasda
              asdasdas
              sadasda
            </div>
          </div>            
        </div>            
        <div class="col-4 ml-0 mr-0 box-pricing p-3 ">
          <div class="box-in">
            <div class="div-header w-100 w-100">
              Best Seller
            </div>
            <div class="div-content w-100 w-100 p-3">
              asdasda
              asdasdas
              sadasda
            </div>
          </div>            
        </div>            
        <div class="col-4 ml-0 mr-0 box-pricing p-3 ">
          <div class="box-in">
            <div class="div-header w-100 w-100">
              Basic
            </div>
            <div class="div-content w-100 w-100 p-3">
              asdasda
              asdasdas
              sadasda
            </div>
          </div>
        </div>
        
        
      </div>
      <div class="col-1 ">
      </div>
      <div class="col-4 ">
        <label>
          25.000 Kontak / Tahun
        </label>
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
