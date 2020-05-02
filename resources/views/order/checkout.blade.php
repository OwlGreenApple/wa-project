@extends('layouts.app')
@section('content')
<link rel="stylesheet" href="{{asset('assets/css/checkout.css')}}">

<div class="container" style="margin-top:50px; margin-bottom:100px">
  <div class="row justify-content-center">
    <div class="col-md-8 col-12">
      <div class="card-custom">
        <div class="card cardpad">


          @if (session('error') )
            <div class="col-md-12 alert alert-danger">
              {{session('error')}}
            </div>
          @endif

          <?php if (Auth::check()) {?>
          <form method="POST" action="{{url('submit-checkout')}}">
            <?php } else {?>
            <form method="POST" action="{{url('submit-checkout-register')}}">
              <?php }?>
              {{ csrf_field() }}
              <input type="hidden" id="price" name="price">
              <input type="hidden" id="namapaket" name="namapaket">
              <input type="hidden" id="namapakettitle" name="namapakettitle">
							<input type="hidden" id="priceupgrade" name="priceupgrade">
              <h2 class="Daftar-Disini">Choose your package</h2>
							<script>
								dayleft = 0;priceupgrade=0;totalPriceUpgrade=0;
							</script>
							<?php if (Auth::check()) {?>
								<div class="form-group">
									<div class="col-md-12 col-12">
										<label class="label-title-test" for="">
											Remaining Time Upgrade:
										</label>

										<label id="label-priceupgrade"></label>
									</div>
								</div>
							<?php }?>
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <label class="text" for="formGroupExampleInput">Your package:</label>
                  <select class="form-control" name="idpaket" id="select-auto-manage">
                        <option class="" data-price="195000" data-paket="basic1" data-paket-title="Basic - 15.000 Messages" value="1" <?php if ($id==1) echo "selected" ; ?>>
                          Basic - 15.000 Messages - IDR 195.000,-/30 days
                        </option>
                        <option class="" data-price="370500" data-paket="bestseller1" data-paket-title="Best Seller - 15.000 Messages" value="2" <?php if ($id==2) echo "selected" ; ?>>
                          Best Seller - 15.000 Messages - IDR 370.500,-/60 days 
                        </option>
                        <option class="" data-price="526500" data-paket="supervalue1" data-paket-title="Super Value - 15.000 Messages" value="3" <?php if ($id==3) echo "selected" ; ?>>
                          Super Value - 15.000 Messages - IDR 526.500,-/90 days 
                        </option>

                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="275000" data-paket="basic2" data-paket-title="Basic - 25.000 Messages" value="4" <?php if ($id==4) echo "selected" ; ?>>
                          Basic - 25.000 Messages - IDR 275.000,-/30 days
                        </option>
                        <option class="" data-price="522500" data-paket="bestseller2" data-paket-title="Best Seller - 25.000 Messages" value="5" <?php if ($id==5) echo "selected" ; ?>>
                          Best Seller - 25.000 Messages - IDR 522.500,-/60 days 
                        </option>
                        <option class="" data-price="742500" data-paket="supervalue2" data-paket-title="Super Value - 25.000 Messages" value="6" <?php if ($id==6) echo "selected" ; ?>>
                          Super Value - 25.000 Messages - IDR 742.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="345000" data-paket="basic3" data-paket-title="Basic - 40.000 Messages" value="7" <?php if ($id==7) echo "selected" ; ?>>
                          Basic - 40.000 Messages - IDR 345.000,-/30 days
                        </option>
                        <option class="" data-price="655500" data-paket="bestseller3" data-paket-title="Best Seller - 40.000 Messages" value="8" <?php if ($id==8) echo "selected" ; ?>>
                          Best Seller - 40.000 Messages - IDR 655.500,-/60 days 
                        </option>
                        <option class="" data-price="931500" data-paket="supervalue3" data-paket-title="Super Value - 40.000 Messages" value="9" <?php if ($id==9) echo "selected" ; ?>>
                          Super Value - 40.000 Messages - IDR 931.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="415000" data-paket="basic4" data-paket-title="Basic - 60.000 Messages" value="10" <?php if ($id==10) echo "selected" ; ?>>
                          Basic - 60.000 Messages - IDR 415.000,-/30 days
                        </option>
                        <option class="" data-price="788500" data-paket="bestseller4" data-paket-title="Best Seller - 60.000 Messages" value="11" <?php if ($id==11) echo "selected" ; ?>>
                          Best Seller - 60.000 Messages - IDR 788.500,-/60 days 
                        </option>
                        <option class="" data-price="1120500" data-paket="supervalue4" data-paket-title="Super Value - 60.000 Messages" value="12" <?php if ($id==12) echo "selected" ; ?>>
                          Super Value - 60.000 Messages - IDR 1.120.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="555000" data-paket="basic5" data-paket-title="Basic - 90.000 Messages" value="13" <?php if ($id==13) echo "selected" ; ?>>
                          Basic - 90.000 Messages - IDR 555.000,-/30 days
                        </option>
                        <option class="" data-price="1054500" data-paket="bestseller5" data-paket-title="Best Seller - 90.000 Messages" value="14" <?php if ($id==14) echo "selected" ; ?>>
                          Best Seller - 90.000 Messages - IDR 1.054.500,-/60 days 
                        </option>
                        <option class="" data-price="1498500" data-paket="supervalue5" data-paket-title="Super Value - 90.000 Messages" value="15" <?php if ($id==15) echo "selected" ; ?>>
                          Super Value - 90.000 Messages - IDR 1.498.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="695000" data-paket="basic6" data-paket-title="Basic - 130.000 Messages" value="16" <?php if ($id==16) echo "selected" ; ?>>
                          Basic - 130.000 Messages - IDR 695.000,-/30 days
                        </option>
                        <option class="" data-price="1320500" data-paket="bestseller6" data-paket-title="Best Seller - 130.000 Messages" value="17" <?php if ($id==17) echo "selected" ; ?>>
                          Best Seller - 130.000 Messages - IDR 1.320.500,-/60 days 
                        </option>
                        <option class="" data-price="1876500" data-paket="supervalue6" data-paket-title="Super Value - 130.000 Messages" value="18" <?php if ($id==18) echo "selected" ; ?>>
                          Super Value - 130.000 Messages - IDR 1.876.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="975000" data-paket="basic7" data-paket-title="Basic - 190.000 Messages" value="19" <?php if ($id==19) echo "selected" ; ?>>
                          Basic - 190.000 Messages - IDR 975.000,-/30 days
                        </option>
                        <option class="" data-price="1852500" data-paket="bestseller7" data-paket-title="Best Seller - 190.000 Messages" value="20" <?php if ($id==20) echo "selected" ; ?>>
                          Best Seller - 190.000 Messages - IDR 1.852.500,-/60 days 
                        </option>
                        <option class="" data-price="2632500" data-paket="supervalue7" data-paket-title="Super Value - 190.000 Messages" value="21" <?php if ($id==21) echo "selected" ; ?>>
                          Super Value - 190.000 Messages - IDR 2.632.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="1255000" data-paket="basic8" data-paket-title="Basic - 250.000 Messages" value="22" <?php if ($id==22) echo "selected" ; ?>>
                          Basic - 250.000 Messages - IDR 1.255.000,-/30 days
                        </option>
                        <option class="" data-price="2384500" data-paket="bestseller8" data-paket-title="Best Seller - 250.000 Messages" value="23" <?php if ($id==23) echo "selected" ; ?>>
                          Best Seller - 250.000 Messages - IDR 2.384.500,-/60 days 
                        </option>
                        <option class="" data-price="3388500" data-paket="supervalue8" data-paket-title="Super Value - 250.000 Messages" value="24" <?php if ($id==24) echo "selected" ; ?>>
                          Super Value - 250.000 Messages - IDR 3.388.500,-/90 days 
                        </option>
												
                        <option class="">
                          -----------------------------------------------------------------------------------------------------------------
                        </option>
												
                        <option class="" data-price="1555000" data-paket="basic9" data-paket-title="Basic - 330.000 Messages" value="25" <?php if ($id==25) echo "selected" ; ?>>
                          Basic - 330.000 Messages - IDR 1.555.000,-/30 days
                        </option>
                        <option class="" data-price="2954500" data-paket="bestseller9" data-paket-title="Best Seller - 330.000 Messages" value="26" <?php if ($id==26) echo "selected" ; ?>>
                          Best Seller - 330.000 Messages - IDR 2.954.500,-/60 days 
                        </option>
                        <option class="" data-price="4288500" data-paket="supervalue9" data-paket-title="Super Value - 330.000 Messages" value="27" <?php if ($id==27) echo "selected" ; ?>>
                          Super Value - 330.000 Messages - IDR 4.288.500,-/90 days 
                        </option>
												
                  </select>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <label class="label-title-test" for="formGroupExampleInput">
                    Coupon code (optional):
                  </label>

                  <input type="text" class="form-control form-control-lg" name="kupon" id="kupon" placeholder="Kode Kupon Anda" style="width:100%">  
                  <button type="button" class="btn btn-primary btn-kupon  form-control-lg col-md-3 col-sm-12 col-xs-12 mt-3">
                    Apply
                  </button>  
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <div id="pesan" class="alert"></div>
                </div>
              </div>
              
              <div class="form-group">
                <div class="col-md-12 col-12">
                  <label class="label-title-test" for="formGroupExampleInput">
                    Total: 
                  </label>
                  <div class="col-md-12 pl-0">
                    <span class="total" style="font-size:18px"></span>
                  </div>  
                </div>
              </div>
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" required/>
                  <label for="agree-term" class="label-agree-term text">I agree to all statements in <a href="{{url('/helps')}}" class="term-service" target="_blank">Terms of service</a></label>
                </div>
              </div>
              <div class="form-group">
                <div class="col-12 col-md-12">
                  <input type="submit" name="submit" id="submit" class="col-md-12 col-12 btn btn-primary bsub btn-block" value="Order Now"/>
                </div>
              </div>
            </form>
        </div>
      </div>
    </div>
  </div>
</div>

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

  $(document).ready(function() {
    $("body").on("click", ".btn-kupon", function() {
      check_kupon();
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