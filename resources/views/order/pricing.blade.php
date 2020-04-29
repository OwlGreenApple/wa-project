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
			<i class="fa fa-check"></i>
      -->
      <h1 class="col-12">
        Choose Your Package
      </h1>
      <div class="col-1 ">
      </div>
      <div class="col-md-7 col-lg-7 col-sm-12 col-xs-12 price-content">
				<div class="row">
        <h3><br><br>
          How many messages you need to send per month ? 
        </h3>
        <div class="col-10 div-range-slider">
          <input type="text" class="js-range-slider" name="my_range" value="" />
        </div>
        <div class="col-4 ml-0 mr-0 box-pricing p-2 " data-attr="1">
          <div class="box-in">
            <div class="div-header w-100 p-1">
              <h5>Basic</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>Monthly </span>
              <span>&nbsp</span>
              <span id="monthly-price">IDR 195.000</span>
              <span id="description-contact-3" class="description-contact">15.000 Messages</span>
            </div>
          </div>            
        </div>    
        <div class="col-4 ml-0 mr-0 box-pricing p-2 selected" data-attr="2">
          <div class="box-in">
            <div class="div-header w-100 p-1">
              <i class=""></i> 
              <h5 class="pl-3">Best Seller</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>2 Months </span>
              <span>&nbsp</span>
              <span id="2months-price">IDR 370.500</span>
              <span id="description-contact-3" class="description-contact">15.000 Messages</span>
            </div>
          </div>            
        </div>    
        <div class="col-4 ml-0 mr-0 box-pricing p-2" data-attr="3">
          <div class="box-in">
            <div class="div-header w-100 p-1">
              <h5>Super Value</h5>
            </div>
            <div class="div-content w-100 w-100 p-3">
              <span>3 Months </span>
              <span>&nbsp</span>
              <span id="3months-price">IDR 526.500</span>
              <span id="description-contact-3" class="description-contact">15.000 Messages</span>
            </div>
          </div>            
        </div>            
        </div>
      </div>
			<!--
      <div class="col-1 ">
      </div>
			-->
      <div class="col-md-4 col-lg-4 col-xs-12 col-sm-12 detail-description">
        <h3 id="description-contact-header">Best Seller 3 Months<br>15.000 Messages/month</h3>
        <button type="button" class="btn btn-lg btn-success col-12" id="choose-price">IDR 2.970.000</button>
        <ul>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span>Unlimited Contacts</span>
          </li>
          <li>
            <i class="fa fa-check" aria-hidden="true"></i> 
            <span id="message-per-month">10.000 messages / month </span>
          </li>
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
            <i class="not-15000 fa fa-check" aria-hidden="true"></i> 
            <span>Event Reminder</span>
          </li>
          <li>
            <i class="not-15000 fa fa-check" aria-hidden="true"></i> 
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
			if (messagePer30day == 15000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/1'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>15.000 Messages/month");
			}
			if (messagePer30day == 25000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/4'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>25.000 Messages/month");
			}
			if (messagePer30day == 40000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/7'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>40.000 Messages/month");
			}
			if (messagePer30day == 60000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/10'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>60.000 Messages/month");
			}
			if (messagePer30day == 90000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/13'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>90.000 Messages/month");
			}
			if (messagePer30day == 130000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/16'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>130.000 Messages/month");
			}
			if (messagePer30day == 190000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/19'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>190.000 Messages/month");
			}
			if (messagePer30day == 250000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/22'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>250.000 Messages/month");
			}
			if (messagePer30day == 330000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/25'); ?>");
				$("#description-contact-header").html("Basic 1 Month<br>330.000 Messages/month");
			}
		}
		if (modePrice==2) {
			if (messagePer30day == 15000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/2'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>15.000 Messages/month");
			}
			if (messagePer30day == 25000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/5'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>25.000 Messages/month");
			}
			if (messagePer30day == 40000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/8'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>40.000 Messages/month");
			}
			if (messagePer30day == 60000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/11'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>60.000 Messages/month");
			}
			if (messagePer30day == 90000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/14'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>90.000 Messages/month");
			}
			if (messagePer30day == 130000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/17'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>130.000 Messages/month");
			}
			if (messagePer30day == 190000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/20'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>190.000 Messages/month");
			}
			if (messagePer30day == 250000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/23'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>250.000 Messages/month");
			}
			if (messagePer30day == 330000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/26'); ?>");
				$("#description-contact-header").html("Best Seller 2 Months<br>330.000 Messages/month");
			}		
		}
		if (modePrice==3) {
			if (messagePer30day == 15000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/3'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>15.000 Messages/month");
			}
			if (messagePer30day == 25000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/6'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>25.000 Messages/month");
			}
			if (messagePer30day == 40000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/9'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>40.000 Messages/month");
			}
			if (messagePer30day == 60000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/12'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>60.000 Messages/month");
			}
			if (messagePer30day == 90000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/15'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>90.000 Messages/month");
			}
			if (messagePer30day == 130000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/18'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>130.000 Messages/month");
			}
			if (messagePer30day == 190000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/21'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>190.000 Messages/month");
			}
			if (messagePer30day == 250000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/24'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>250.000 Messages/month");
			}
			if (messagePer30day == 330000) {
				$("#link-checkout").attr("href","<?php echo url('checkout/27'); ?>");
				$("#description-contact-header").html("Super Value 3 Months<br>330.000 Messages/month");
			}
		}
	}
  function set_price(){
      if (modePrice==1) {
        $("#choose-price").html($("#monthly-price").html());
      }
      if (modePrice==2) {
        $("#choose-price").html($("#2months-price").html());
      }
      if (modePrice==3) {
        $("#choose-price").html($("#3months-price").html());
      }

      messagePer30day = $(".js-range-slider").val();
      if (messagePer30day == 15000) {
        $(".not-15000").removeClass("fa-check");
        $(".not-15000").addClass("fa-times");
      }
      else {
        $(".not-15000").addClass("fa-check");
        $(".not-15000").removeClass("fa-times");
      }

  }
  
  function slider_init(){
    var custom_values = [15000, 25000, 40000, 60000, 90000, 130000, 190000, 250000, 330000];
    $(".js-range-slider").ionRangeSlider({
        grid: true,
        step:100,
        skin: "round",
        values: custom_values,
        onChange: function (data) {
            // fired on every range slider update
            // console.log(data);
            // console.log($(".js-range-slider").val());
          messagePer30day = $(".js-range-slider").val();
          if (messagePer30day == 15000) {
            $("#monthly-price").html("IDR 195.000");
            $("#2months-price").html("IDR 370.500");
            $("#3months-price").html("IDR 526.500");
            $("#message-per-month").html("1.000 Messages / day ");
            $(".description-contact").html("15.000 Messages");
          }
          if (messagePer30day == 25000) {
            $("#monthly-price").html("IDR 275.000");
            $("#2months-price").html("IDR 522.500");
            $("#3months-price").html("IDR 742.500");
            $("#message-per-month").html("1.500 Messages / day ");
            $(".description-contact").html("25.000 Messages");
          }
          if (messagePer30day == 40000) {
            $("#monthly-price").html("IDR 345.000");
            $("#2months-price").html("IDR 655.500");
            $("#3months-price").html("IDR 931.500");
            $("#message-per-month").html("2.000 Messages / day ");
            $(".description-contact").html("40.000 Messages");
          }
          if (messagePer30day == 60000) {
            $("#monthly-price").html("IDR 415.000");
            $("#2months-price").html("IDR 788.500");
            $("#3months-price").html("IDR 1.120.500");
            $("#message-per-month").html("2.500 Messages / day ");
            $(".description-contact").html("60.000 Messages");
          }
          if (messagePer30day == 90000) {
            $("#monthly-price").html("IDR 555.000");
            $("#2months-price").html("IDR 1.054.500");
            $("#3months-price").html("IDR 1.498.500");
            $("#message-per-month").html("3.000 Messages / day ");
            $(".description-contact").html("90.000 Messages");
          }
          if (messagePer30day == 130000) {
            $("#monthly-price").html("IDR 695.000");
            $("#2months-price").html("IDR 1.320.500");
            $("#3months-price").html("IDR 1.876.500");
            $("#message-per-month").html("3.500 Messages / day ");
            $(".description-contact").html("130.000 Messages");
          }
          if (messagePer30day == 190000) {
            $("#monthly-price").html("IDR 975.000");
            $("#2months-price").html("IDR 1.852.500");
            $("#3months-price").html("IDR 2.632.500");
            $("#message-per-month").html("4.000 Messages / day ");
            $(".description-contact").html("190.000 Messages");
          }
          if (messagePer30day == 250000) {
            $("#monthly-price").html("IDR 1.255.000");
            $("#2months-price").html("IDR 2.384.500");
            $("#3months-price").html("IDR 3.388.500");
            $("#message-per-month").html("4.500 Messages / day ");
            $(".description-contact").html("250.000 Messages");
          }
          if (messagePer30day == 330000) {
            $("#monthly-price").html("IDR 1.555.000");
            $("#2months-price").html("IDR 2.954.500");
            $("#3months-price").html("IDR 4.288.500");
            $("#message-per-month").html("5.000 Messages / day ");
            $(".description-contact").html("330.000 Messages");
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
