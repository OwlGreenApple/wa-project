@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{asset('assets/css/summary.css')}}">

<script src="https://www.google.com/recaptcha/api.js?render=<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>"></script>
<script>
  grecaptcha.ready(function() {
    grecaptcha.execute("<?php echo env('GOOGLE_RECAPTCHA_SITE_KEY');?>", {action: 'contact_form'}).then(function(token) {
        $('#recaptchaResponse').val(token);
    });
  });
</script>

<?php 
	$is_login = false;
	if (Auth::check()) {
		$is_login = true;
	}
?>
<section>
    <div class="container">
      <div class="row sumo-split-layout">
        

<div class="col-md-6 sumo-col-left pb-0 pb-md-50">
  <div class="mt-50 my-md-50 mr-lg-50 pr-md-30">
    <div class="pb-20">
      <h1 class="mb-0">Checkout</h1>
    </div>
    <a class="d-inline-block d-md-none pb-30" data-toggle="modal" data-target="#summaryModal">
      <span class="d-flex align-items-center">
        <img class="mr-10" src="https://appsumo2.b-cdn.net/static/images/svg/baseline-shopping_cart-24px.svg" width="auto" height="24">
        <span class="sumo-psuedo-link">View cart summary</span>
      </span>
    </a>
    <div id="checkoutSteps">
      <!-- Card 1 -->
      <div class="checkout-step-container mb-30">
        <div class="card checkout-card card-step-1 filled" id="cardStep1">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h2 class="h3">1. <?php if ($is_login) { ?>Account verification<?php } else { ?>Create an account <?php } ?></h2>
          </div>
        </div>
        <!-- End Card Header -->
        <div class="card-body">
          <!-- Card Data Summary -->
          <div class="card-data-summary show">
						<?php if ($is_login) { ?>
            <p>Your order confirmation will be emailed to:</p>
            <span class="sumo-psuedo-link">{{Auth::user()->email}}</span>
						<?php } else { ?>

							<div id="div-register">
                <form class="add-contact" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="form-group">
                      <label>Name*</label>
                      <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Input Your Name" required />
                      @error('username')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label>Email*</label>
                       <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Input Your Email">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                      <label>Handphone* <span class="tooltipstered" title="<div class='panel-heading'>Message</div><div class='panel-content'>
                            Fill with your phone number without 0 or country code<br/>
                            For example : 8123456789, (201)5555555
                          </div>">
                          <i class="fa fa-question-circle "></i>
                        </span>
                      </label>
                      <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required/>
                      @error('phone')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                      <input id="hidden_country_code" type="hidden" class="form-control @error('code_country') is-invalid @enderror" name="code_country" />
                      @error('code_country')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                     <input name="data_country" type="hidden" /> 
                    </div>

                    <div class="form-group">
                      <label>Gender*</label>
                      <div>
                        <div class="form-check form-check-inline">
                          <label class="custom-radio">
                            <input class="form-check-input" type="radio" name="gender" value="male" id="radio-male" checked>
                            <span class="checkmark"></span>
                          </label>
                          <label class="form-check-label" for="radio-male">Male</label>
                        </div>

                        <div class="form-check form-check-inline">
                          <label class="custom-radio">
                            <input class="form-check-input" type="radio" name="gender" id="radio-female" value="female">
                            <span class="checkmark"></span>
                          </label>
                          <label class="form-check-label" for="radio-female">Female</label>
                        </div>

                      </div>
                      <!-- -->
                    </div>

                    <div class="form-group">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="agreement" required id="check-terms"/>
                            <span class="checkmark-check"></span>
                        </label>
                        <label class="checkbox-left" for="check-terms"><sb>I Agree with <a>Terms and Condition</a></sb></label>
                    </div>

                    <div class="text-left">
                      <button id="btn-register" type="button" class="btn btn-custom btn-lg">REGISTER</button>
                    </div>
                    <input type="hidden" name="recaptcha_response" id="recaptchaResponse" readonly="readonly"/>
                </form>

                <hr class="mt-5" />

                <div class="mt-4 mb-3"><sb>Already Have An Account? <a href="" id="link-to-login">Log in Here</a></sb></div>
							</div>
							<div id="div-login" style="display:none;">
								<form class="add-contact" method="POST" action="{{ route('login') }}">
										@csrf
										 <div class="form-group">
												<label>Email*</label>
												 <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }} {{ Cookie::get('email') }}" required autocomplete="email" placeholder="Input Your Email">

													@error('email')
															<span class="invalid-feedback" role="alert">
																	<strong>{{ $message }}</strong>
															</span>
													@enderror
											</div>

											<div class="form-group">
												<label>Password *</label>
												 <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" value="{{ Cookie::get('password') }}" placeholder="Input Your Password">
												
												 @error('password')
															<span class="invalid-feedback" role="alert">
																	<strong>{{ $message }}</strong>
															</span>
												 @enderror
											</div>

											<div class="form-group">
												<label class="custom-checkbox">
														<input type="checkbox" name="remember"/>
														<span class="checkmark-check"></span>
												</label>
												<label class="checkbox-left"><sb>Remember Me</sb></label>
											</div>


											<div class="text-left">
												<button type="button" id="button-login" class="btn btn-custom btn-lg">LOG IN</button>
											</div>
								</form>

								<hr class="mt-5" />

								<div class="mt-4 mb-3"><sb>Need a Activtele account? <a href="" id="link-to-register">Register Here</a></sb></div>
							</div>
						<?php } ?>
          </div>
        </div>
        </div>
      </div>

      <!-- Card 2 -->
      <div class="checkout-step-container">
        <div class="card checkout-card card-step-2 inactive" id="cardStep2">
        <div class="card-header">
          <div class="d-flex align-items-center">
            <h2 class="h3">2. Order Review</h2>
          </div>
        </div>
        <!-- End Card Header -->
        <div class="card-body">
          <!-- Card Data Entry -->
          <div class="card-data-entry hide"> <!-- style="display: none;" -->
            <p>
              <b>
                While we're sure you got everything right, please review your order summary,
                email address, and payment method before placing your order.
              </b>
            </p>
            <!-- Mobile Checkout Summary  -->
            <div id="cart-checkout-summary-mobile" class="d-block d-md-none">
              
<!-- Table -->
<table class="table sumo-purchases-table">
  <tbody>
    
      <tr>
        <td class="sumo-td-img">
          
            
              <img class="rounded-border" src="https://appsumo2.b-cdn.net/media/cache/76/b2/76b25a10485370cb56ef7795e21883c9.jpg" width="25" height="auto">
            
          
        </td>
        <td class="sumo-td-name">
          <div class="sumo-title">
            <b>SuiteDash</b>
            
          </div>
          <div class="quantity">QTY: 1</div>
        </td>
        <td class="sumo-td-price text-right sumo-checkout-item cart-item" data-item-id="2101">$79.00
        
        </td>
      </tr>
    
  </tbody>
</table>
<!-- End Table -->
<div>
    <div class="as-checkout-entry">
      <strong>Subtotal</strong>
      <strong>$79.00</strong>
    </div>
    <!-- TODO: ANNIVERSARY_PROMOTION_OVER? remove me -->
    
    
    
    
    <div class="as-checkout-entry" id="checkout-total" data-total="79.00">
      <strong class="as-checkout-total">Total</strong>
      <strong class="as-checkout-total-price" id="totalprice_sidebar totalprice_mobile">$ 79.00</strong>
    </div>
</div>





            </div>
            <!-- Checkout Button Container -->
            <div class="checkout-button-container mt-30 show d-md-none" id="checkout-buttons-2">
              <button type="submit" class="btn btn-secure-checkout full-width hide waves-effect waves-light" data-pay-select="option1">
                <img src="https://appsumo2.b-cdn.net/static/images/svg/baseline-lock-24px.svg" width="auto" height="20">
                <span>Place Order via Secure Checkout</span>
              </button>
              <button type="submit" class="btn btn-paypal-checkout full-width hide waves-effect waves-light" data-pay-select="option2">
                <img src="https://appsumo2.b-cdn.net/static/images/checkout/payment-methods/paypal.svg" width="auto" height="17">
                <span>Checkout</span>
              </button>
              <button type="submit" class="btn btn-g-pay-checkout full-width hide waves-effect waves-light" data-pay-select="option3">
                <span>Order with</span>
                <img src="https://appsumo2.b-cdn.net/static/images/checkout/payment-methods/gpay-white.svg" width="auto" height="17">
              </button>
              <button type="submit" class="btn btn-apple-pay-checkout full-width hide waves-effect waves-light" data-pay-select="option4">
                <span>Order with</span>
                <img src="https://appsumo2.b-cdn.net/static/images/checkout/payment-methods/applepay-white.svg" width="auto" height="17">
              </button>
              <div class="sumo-product-note light mt-20">
                By clicking the "Place Order" button, you confirm that you have read, understand,
                and accept our Terms and Conditions, Return Policy, and Privacy Policy.
              </div>
            </div>
            <!-- End Mobile Checkout Summary -->
            <hr class="my-30">
            <h4 class="mb-10"><b>Need Help?</b></h4>
            <p>
              Our support team is only one click away! Send us any questions you may have.
            </p>
            <a href="https://help.appsumo.com" target="_blank" class="btn btn-more full-width-mobile waves-effect waves-light">Find Help</a>
          </div>
          <!-- End Card Data Entry -->
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

        
        <!-- Right Column -->
        <div class="col-md-6 sumo-col-right pb-50">
          <div class="mt-md-50 mb-50 ml-lg-50 pl-md-30">
            <div class="pb-20 d-none d-md-block">
              <div class="placeholder"></div>
            </div>
            <!-- Summary -->
            <div class="card checkout-card checkout-summary dark mb-30 d-none d-md-flex">
              <div class="card-header">
                <div class="d-flex align-items-center">
                  <h2 class="h3">Summary</h2>
                  <a class="edit-link ml-auto" href="<?php echo url()->previous(); ?>">Edit</a>
                </div>
              </div>
              <!-- End Card Header -->
              <div class="card-body pt-20">
                
<div id="cart-checkout-summary">
  <!-- Desktop Table -->
  <!-- Table -->
  <table class="table sumo-purchases-table">
    <tbody>
      
        <tr>
          <td class="sumo-td-img">
            
              
                <img class="rounded-border" src="https://appsumo2.b-cdn.net/media/cache/76/b2/76b25a10485370cb56ef7795e21883c9.jpg" width="25" height="auto">
              
            
          </td>
          <td class="sumo-td-name">
            <div class="sumo-title">
              <b>SuiteDash</b>
              
            </div>
            <div class="quantity">QTY: 1</div>
          </td>
          <td class="sumo-td-price text-right sumo-checkout-item" data-item-id="2101">$ 79.00
            

            
          </td>
        </tr>
      
    </tbody>
  </table>
  <!-- End Table -->
  <div>
    <div class="as-checkout-entry">
      <strong>Subtotal</strong>
      <strong>$79.00</strong>
    </div>
    <!-- TODO: ANNIVERSARY_PROMOTION_OVER? remove me -->
    
    
    
    
    <div class="as-checkout-entry" id="checkout-total" data-total="79.00">
      <strong class="as-checkout-total">Total</strong>
      <strong class="as-checkout-total-price" id="totalprice_sidebar totalprice_mobile">$ 79.00</strong>
    </div>
</div>
  
  
  
  <!-- Checkout Button Container -->
  <form id="cart-checkout-form" class="d-none" action="/checkout/" method="post">
    <input type="hidden" name="csrfmiddlewaretoken" value="kz6X1dZDJ2V5PIibeTaIOypQC4EZgJj9An4FFSMe7gXoJcqiIZjNHxYwPEecHWHF">
  </form>
</div>







                
                  
                    
                        <div class="checkout-button-container mt-30 hide" id="checkout-buttons-1">
                      <button type="submit" class="btn btn-secure-checkout full-width hide waves-effect waves-light" data-pay-select="option1">
                        <img src="https://appsumo2.b-cdn.net/static/images/svg/baseline-lock-24px.svg" width="auto" height="20">
                        <span>Place Order via Secure Checkout</span>
                      </button>
                      <button type="submit" class="btn btn-paypal-checkout full-width hide waves-effect waves-light" data-pay-select="option2">
                        <img src="https://appsumo2.b-cdn.net/static/images/checkout/payment-methods/paypal.svg" width="auto" height="17">
                        <span>Checkout</span>
                      </button>
                      <button type="submit" class="btn btn-g-pay-checkout full-width hide waves-effect waves-light" data-pay-select="option3">
                        <span>Order with</span>
                        <img src="https://appsumo2.b-cdn.net/static/images/checkout/payment-methods/gpay-white.svg" width="auto" height="17">
                      </button>
                      <button type="submit" class="btn btn-apple-pay-checkout full-width hide waves-effect waves-light" data-pay-select="option4">
                        <span>Order with</span>
                        <img src="https://appsumo2.b-cdn.net/static/images/checkout/payment-methods/applepay-white.svg" width="auto" height="17">
                      </button>
                      <div class="sumo-product-note light mt-20">
                        By clicking the "Place Order" button, you confirm that you have read, understand,
                        and accept our Terms and Conditions, Return Policy, and Privacy Policy.
                      </div>
                    </div>
                    
                  
                
              <!-- Close Desktop Table -->
              </div>
            </div>
            <!-- Supplementary Info -->
            <div class="sumo-cart-supplement mt-30">
              <p class="sumo-cart-supplement-header">Hustle with Confidence</p>
              <ul class="list-inline mt-20">
                
                <li class="d-flex">
                  <img class="sumo-icon" width="auto" height="20px" src="https://appsumo2.b-cdn.net/static/images/svg/calendar.svg">
                  <span><b>Try any product risk free.</b> We offer an industry-best 60-day money-back guarantee
                    — no questions asked. So go ‘head and take any of our products for a spin to see if they’re
                    a good fit for your business.</span>
                </li>
                <li class="d-flex">
                  <img class="sumo-icon" width="auto" height="20px" src="https://appsumo2.b-cdn.net/static/images/svg/lifebuoy.svg">
                  <span><b>World-class customer support.</b> There’s customer support,
                    and then there’s AppSumo customer support. We take pride in going
                    above and beyond to solve issues and keep our community happy.</span>
                </li>
                <li class="d-flex">
                  <img class="sumo-icon" width="auto" height="20px" src="https://appsumo2.b-cdn.net/static/images/svg/message-text.svg">
                  <span><b>Access to founders and CEOs.</b> As an early adopter,
                    you have the CEO’s ear — ask your burning questions on
                    any active deal and have them answered by the product founders themselves.</span>
                </li>
              </ul>
            </div>
            <!-- End Supplemetary Info -->
          </div>
        </div>
      </div>
    </div>
  </section>

<script type="text/javascript">

  function check_kupon(){
    $.ajax({
      type: 'POST',
      url: "{{url('/check-coupon')}}",
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: {
        harga : $('#price').val(),
        kupon : $('#kupon').val(),
        idpaket : $( "#select-auto-manage" ).val(),
      },
      dataType: 'text',
      beforeSend: function() {
        $('#loader').show();
        $('.div-loading').addClass('background-load');
      },
      success: function(result) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');

        var data = jQuery.parseJSON(result);

        $('#pesan').html(data.message);
        $('#pesan').show();
        if (data.message=="") {
          $('#pesan').hide();
        }
        
        if (data.status == 'success') {
          $('.total').html('IDR ' + formatNumber(parseInt(data.total)+parseInt(totalPriceUpgrade)));
          $('#pesan').removeClass('alert-danger');
          $('#pesan').addClass('alert-success');
        } 
        else if (data.status == 'success-paket') {
          $('.total').html('IDR ' + formatNumber(parseInt(data.total)+parseInt(totalPriceUpgrade)));
          $('#pesan').removeClass('alert-danger');
          $('#pesan').addClass('alert-success');
          
          flagSelect = false;
          $("#select-auto-manage option").each(function() {
            console.log($(this).val());
            if ($(this).val() == data.paketid) {
              flagSelect = true;
            }
          });

          if (flagSelect == false) {
            labelPaket = data.paket;
            if (data.kodekupon=="SPECIAL12") {
              labelPaket = "Paket Special Promo 1212 - IDR 295.000";
            }
            $('#select-auto-manage').append('<option value="'+data.paketid+'" data-price="'+data.dataPrice+'" data-paket="'+data.dataPaket+'" selected="selected">'+labelPaket+'</option>');
          }
          $("#price").val(data.dataPrice);
          $("#namapaket").val(data.dataPaket);
          
          $('#select-auto-manage').val(data.paketid);
          $( "#select-auto-manage" ).change();
        }
        else {
          $('#pesan').removeClass('alert-success');
          $('#pesan').addClass('alert-danger');
        }
      }
    });
  }
  
	function formatNumber(num) {
		return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
	}


	/*
	REGISTER
	*/
  function getDataFromCountry()
  {
     var data_country = $(".iti__selected-flag").attr('data-country');
     $("input[name='data_country']").val(data_country);
  }

  function countryChange()
  {
     jQuery("#phone").on('countrychange', function(e, countryData){
        var data_country = $(".iti__selected-flag").attr('data-country');
        $("input[name='data_country']").val(data_country);
    })
  } 

  function fixLayoutInputPhoneCountry()
  {
      $(".iti").addClass('w-100');
  }

  function agreement(){
    $("input[name=agreement]").click(function(){
      var val = $(this).val();

      if(val == 1){
        $(this).val('on');
      }
      else {
        $(this).val(1);
      }

    });
  }

  function rememberMe(){
    $("input[name=remember]").click(function(){
      var val = $(this).val();

      if(val == 1){
        $(this).val('on');
      }
      else {
        $(this).val(1);
      }

    });
  }

	function loginAjax(){
    $("body").on("click", "#button-login", function() {
			$.ajax({
				type: 'POST',
				url: "{{url('/check-coupon')}}",
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				data: {
					harga : $('#price').val(),
					kupon : $('#kupon').val(),
					idpaket : $( "#select-auto-manage" ).val(),
				},
				dataType: 'text',
				beforeSend: function() {
					$('#loader').show();
					$('.div-loading').addClass('background-load');
				},
				success: function(result) {
					$('#loader').hide();
					$('.div-loading').removeClass('background-load');

					var data = jQuery.parseJSON(result);

					$('#pesan').html(data.message);
					$('#pesan').show();
					if (data.message=="") {
						$('#pesan').hide();
					}
					
					if (data.status == 'success') {
					} 
					else {
						$('#pesan').removeClass('alert-success');
						$('#pesan').addClass('alert-danger');
					}
				}
			});
    });
	}

	function registerAjax(){
    $("#btn-register").click(function(){
      var val= $("input[name=agreement]").val();

      if(val == 'on'){
        alert('Please Check Agreement Box');
      }
    });
	}

  $(document).ready(function() {
		agreement();
		fixLayoutInputPhoneCountry();
		getDataFromCountry();
		countryChange();
		rememberMe();
		
		loginAjax();
		registerAjax();

    $("body").on("click", ".btn-kupon", function() {
      check_kupon();
    });

    $("body").on("click", "#link-to-login", function(e) {
			e.preventDefault();
      $("#div-login").show();
      $("#div-register").hide();
			$('html, body').animate({scrollTop: '0px'}, 300);
    });
    $("body").on("click", "#link-to-register", function(e) {
			e.preventDefault();
      $("#div-login").hide();
      $("#div-register").show();
			$('html, body').animate({scrollTop: '0px'}, 300);
    });

		<?php if (Auth::check()) {?>
			dayleft = <?php echo $dayleft;?>;
			priceupgrade = <?php echo $priceupgrade;?>;
		<?php }?>
		$( "#select-auto-manage" ).change(function() {
			var price = $(this).find("option:selected").attr("data-price");
			var namapaket = $(this).find("option:selected").attr("data-paket");
			var namapakettitle = $(this).find("option:selected").attr("data-paket-title");

			<?php if (Auth::check()) {?>
				totalPriceUpgrade = dayleft * ((price-priceupgrade)/30);
				if (parseInt(totalPriceUpgrade)< 0 ) {
					$("#label-priceupgrade").html("Tidak dapat downgrade");
					totalPriceUpgrade = 0;
				}
				else {
					$("#label-priceupgrade").html("IDR "+formatNumber(totalPriceUpgrade));
				}
				$("#priceupgrade").val(totalPriceUpgrade);
			<?php }?>
			
			$("#price").val(price);
			$("#namapaket").val(namapaket);
			$("#namapakettitle").val(namapakettitle);
			// $('#kupon').val("");
			check_kupon();
		});
		$( "#select-auto-manage" ).change();
		$(".btn-kupon").trigger("click");
  });
    
</script>


@endsection