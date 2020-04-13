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
        <div class="col-4 ml-0 mr-0 box-pricing p-2 selected" data-attr="1">
          <div class="box-in">
            <div class="div-header w-100 p-1">
              <h5>Super Value</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>6 Months </span>
              <span>&nbsp</span>
              <span id="6months-price">IDR 1.053.000</span>
              <span id="description-contact-3">25.000 Contacts</span>
            </div>
          </div>            
        </div>            
        <div class="col-4 ml-0 mr-0 box-pricing p-2 " data-attr="2">
          <div class="box-in">
            <div class="div-header w-100 p-1">
              <i class=""></i> 
              <h5 class="pl-3">Best Seller</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>3 Months </span>
              <span>&nbsp</span>
              <span id="3months-price">IDR 538.200</span>
              <span id="description-contact-3">25.000 Contacts</span>
            </div>
          </div>            
        </div>    
        <div class="col-4 ml-0 mr-0 box-pricing p-2 " data-attr="3">
          <div class="box-in">
            <div class="div-header w-100 p-1">
              <h5>Basic</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>1 Month </span>
              <span>&nbsp</span>
              <span id="monthly-price">IDR 195.000</span>
              <span id="description-contact-3">25.000 Contacts</span>
            </div>
          </div>            
        </div>    
        
      </div>
      <div class="col-1 ">
      </div>
      <div class="col-4 detail-description">
        <h3 id="description-contact-header">
          25.000 Contacts/6 Months
        </h3>
        <button type="button" class="btn btn-lg btn-success col-12" id="choose-price">IDR 2.970.000</button>
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
            <span>Subscription Form</span>
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
            <span>Google form integration</span>
          </li>
          <li>
            <i class="not-1000 fa fa-check" aria-hidden="true"></i> 
            <span>Event Reminder</span>
          </li>
          <li>
            <i class="not-1000 fa fa-check" aria-hidden="true"></i> 
            <span>Appointment Reminder</span>
          </li>
        </ul>
				<a id="link-checkout">
        <button class="btn btn-lg btn-success button-bottom-buynow col-12">Buy Now <i class="fa fa-chevron-right" aria-hidden="true"></i></button>
				</a>
      </div>
  </div>
</div>

<script type="text/javascript">
  var modePrice;
  $(document).ready(function(){
		$("#link-checkout").attr("href","<?php echo url('checkout/3'); ?>");
    slider_init();
    box_pricing_click();
    set_price();
		check_package();
  });
	function check_package(){
		if (modePrice==1) {
			if (numOfContact == 1000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/3'); ?>");
			}
			if (numOfContact == 2500) {
				$("#link-checkout").attr("href","<?php echo url('checkout/6'); ?>");
			}
			if (numOfContact == 5000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/9'); ?>");
			}
			if (numOfContact == 7500) {
				$("#link-checkout").attr("href","<?php echo url('checkout/12'); ?>");
			}
			if (numOfContact == 10000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/15'); ?>");
			}
			if (numOfContact == 15000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/18'); ?>");
			}
			if (numOfContact == 20000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/21'); ?>");
			}
			if (numOfContact == 25000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/24'); ?>");
			}

		}
		if (modePrice==2) {
			if (numOfContact == 1000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/2'); ?>");
			}
			if (numOfContact == 2500) {
				$("#link-checkout").attr("href","<?php echo url('checkout/5'); ?>");
			}
			if (numOfContact == 5000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/8'); ?>");
			}
			if (numOfContact == 7500) {
				$("#link-checkout").attr("href","<?php echo url('checkout/11'); ?>");
			}
			if (numOfContact == 10000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/14'); ?>");
			}
			if (numOfContact == 15000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/17'); ?>");
			}
			if (numOfContact == 20000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/20'); ?>");
			}
			if (numOfContact == 25000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/23'); ?>");
			}
		}
		if (modePrice==3) {
			if (numOfContact == 1000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/1'); ?>");
			}
			if (numOfContact == 2500) {
				$("#link-checkout").attr("href","<?php echo url('checkout/4'); ?>");
			}
			if (numOfContact == 5000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/7'); ?>");
			}
			if (numOfContact == 7500) {
				$("#link-checkout").attr("href","<?php echo url('checkout/10'); ?>");
			}
			if (numOfContact == 10000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/13'); ?>");
			}
			if (numOfContact == 15000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/16'); ?>");
			}
			if (numOfContact == 20000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/19'); ?>");
			}
			if (numOfContact == 25000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/22'); ?>");
			}
		}
	}
  function set_price(){
      if (modePrice==1) {
        $("#choose-price").html($("#6months-price").html());
      }
      if (modePrice==2) {
        $("#choose-price").html($("#3months-price").html());
      }
      if (modePrice==3) {
        $("#choose-price").html($("#monthly-price").html());
      }
      
      numOfContact = $(".js-range-slider").val();
      if (numOfContact == 1000) {
        $(".not-1000").removeClass("fa-check");
        $(".not-1000").addClass("fa-times");
      }
      else {
        $(".not-1000").addClass("fa-check");
        $(".not-1000").removeClass("fa-times");
      }
			
  }
  
  function slider_init(){
    var custom_values = [1000, 2500, 5000, 7500, 10000, 15000, 20000, 25000];
    $(".js-range-slider").ionRangeSlider({
        grid: true,
        step:100,
        skin: "round",
        values: custom_values,
        onChange: function (data) {
            // fired on every range slider update
            // console.log(data);
            // console.log($(".js-range-slider").val());
          numOfContact = $(".js-range-slider").val();
          if (numOfContact == 1000) {
            $("#6months-price").html("IDR 1.053.000");
            $("#3months-price").html("IDR 538.200");
            $("#monthly-price").html("IDR 195.000");
          }
          if (numOfContact == 2500) {
            $("#6months-price").html("IDR 1.485.000");
            $("#3months-price").html("IDR 759.000");
            $("#monthly-price").html("IDR 275.000");
          }
          if (numOfContact == 5000) {
            $("#6months-price").html("IDR 1.863.000");
            $("#3months-price").html("IDR 952.200");
            $("#monthly-price").html("IDR 345.000");
          }
          if (numOfContact == 7500) {
            $("#6months-price").html("IDR 2.241.000");
            $("#3months-price").html("IDR 1.145.400");
            $("#monthly-price").html("IDR 415.000");
          }
          if (numOfContact == 10000) {
            $("#6months-price").html("IDR 2.997.000");
            $("#3months-price").html("IDR 1.531.800");
            $("#monthly-price").html("IDR 555.000");
          }
          if (numOfContact == 15000) {
            $("#6months-price").html("IDR 3.753.000");
            $("#3months-price").html("IDR 1.918.200");
            $("#monthly-price").html("IDR 695.000");
          }
          if (numOfContact == 20000) {
            $("#6months-price").html("IDR 5.265.000");
            $("#3months-price").html("IDR 2.691.000");
            $("#monthly-price").html("IDR 975.000");
          }
          if (numOfContact == 25000) {
            $("#6months-price").html("IDR 6.777.000");
            $("#3months-price").html("IDR 3.463.800");
            $("#monthly-price").html("IDR 1.255.000");
          }
          set_price();
					check_package();
        }
    });
  }
  
  function box_pricing_click(){
    $('body').on('click', '.box-pricing', function (e) {
      $(".box-pricing").removeClass("selected");
      $(this).addClass("selected");
      modePrice = $(this).attr("data-attr");
      set_price();
			check_package();
    });
  }
</script>
@endsection
