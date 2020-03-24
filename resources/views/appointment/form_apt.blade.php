@extends('layouts.app')

@section('content')
<!-- Modal Delete Confirmation -->
<div class="modal fade" id="confirm-delete" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content content-premiumid">
      <div class="modal-header header-premiumid">
        <h5 class="modal-title" id="modaltitle">
          Confirmation Delete
        </h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body text-center">
        <input type="hidden" name="id_phone_number" id="id_phone_number">

        <label><h4>Are you sure want to <i>delete</i> this phone number ?</h4></label>
        <br><br>
        <span class="txt-mode"></span>
        <br>
        
        <div class="col-12 mb-4" style="margin-top: 30px">
          <button class="btn btn-danger btn-block btn-delete-ok" data-dismiss="modal" id="button-delete-phone">
            Yes, Delete Now
          </button>
        </div>
        
        <div class="col-12 text-center mb-4">
          <button class="btn  btn-block btn-delete-ok" data-dismiss="modal">
            Cancel
          </button>  
        </div>
      </div>
    </div>   
  </div>
</div>

<!-- TOP SECTION -->

<div class="container act-tel-apt wrapper">
      <h3 class="title">Form Appointment Reminder</h3>

      <div class="col-md-12 relativ row">
        <input id="display_phone" type="text" class="form-control custom-select-apt" placeholder="Find a contact by phone number">
        <span class="icon-search"></span>

        <div id="display_data" class="search-result col-lg-12">
          <!-- ajax live serach result -->
        </div>
      </div>

      <form id="appt_form">
            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Name</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 text-left row">
                <input name="customer_name" class="form-control" readonly />
                <span class="error customer_name"></span>
              </div>
            </div> 

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Phone Number</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 text-left row">
                <input name="phone_number" class="form-control" readonly />
                <span class="error phone_number"></span>
              </div>
            </div> 

            <div class="form-group row">
              <label class="col-sm-4 col-form-label">Choose Appointment Time :</label>
              <label class="col-sm-1 col-form-label">:</label>
              <div class="col-sm-7 relativity text-left row">
                <input autocomplete="off" id="datetimepicker" type="text" name="date_send" class="form-control custom-select-campaign" />
                <span class="icon-calendar"></span>
                <span class="error date_send"></span>
              </div>
            </div>

            <div class="text-left error db_error"><!-- internal error --></div>

            <div class="text-left mt-4">
              <button id="submit" type="submit" class="btn btn-custom px-4">Submit</button>
            </div>

            <div class="text-left marketing">
              <div>Marketing by</div>
              <div><img src="{{asset('assets/img/marketing-logo.png')}}"/></div>
            </div>
      </form>
<!-- end container -->    
</div>

<!-- Modal Edit Phone -->
  <div class="modal fade child-modal" id="edit-phone" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content -->
      <div class="modal-content">
        <div class="modal-body">
            <div class="alert alert-danger"><!-- error --></div>
            <div class="form-group">
                 <div class="mb-2">
                  <form id="edit_phone_number">
                    <label>Edit Phone Number</label>
                    <div class="form-group">
                      <input type="text" class="form-control" name="edit_phone" />
                    </div>
                 
                    <div class="text-right">
                      <button type="submit" class="btn btn-custom mr-1">Save</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                    </div>
                  </form>
                </div>
                
            </div>
        </div>
      </div>
      
    </div>
  </div>
  <!-- End Modal -->

<script type="text/javascript">

  $(function () {
      $('#datetimepicker').datetimepicker({
        format : 'YYYY-MM-DD HH:mm',
      }); 
      callAjax();
      fillPhoneToForm();
      saveAppointment();
  });

  function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

  function callAjax()
  {
      $("#display_phone").keyup(delay(function (e) {
        var val = $(this).val();
        $(".search-result").show();
        displayPhoneSearch(val);
        //console.log('Time elapsed!', this.value);
      }, 1500))
  }

  function displayPhoneSearch(val)
  {
      $.ajax({
        type : 'GET',
        url : '{{ url("display-customer-phone") }}',
        data : {phone : val, list_id : {!! $list_id !!} },
        dataType : 'html',
        beforeSend : function(){
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $("#display_data").html(result);
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
  }

  function fillPhoneToForm()
  {
    $("body").on("click",".adding-number",function(){
      var phone = $(this).attr('phone');
      var customer_name = $(this).attr('cname');
      var customer_id = $(this).attr('id');

      $("input[name='customer_name']").val(customer_name);
      $("input[name='phone_number']").val(phone);
      $("#submit").attr('data-id',customer_id);
    });
  }

  function saveAppointment()
  {
    $("#appt_form").submit(function(e){
      e.preventDefault();
      var customer_id =  $("#submit").attr('data-id');
      var data = $(this).serializeArray();
      data.push({name:'list_id', value:{!! $list_id !!}}, {name:'campaign_id',value: {!! $id !!}},{name:'customer_id', value : customer_id} );

      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type : 'POST',
        url : '{{ url("save-appt-time") }}',
        data : data,
        dataType : 'json',
        beforeSend : function(){
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
        success : function(result)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          $(".search-result").hide();

          if(result.success == 1)
          {
            alert(result.message);
            clearForm();
            $(".error").hide();
          }
          else
          {
            $(".error").show();
            $(".customer_name").html(result.customer_name);
            $(".phone_number").html(result.phone_number);
            $(".date_send").html(result.date_send);
            $(".db_error").html(result.customer_id);
            $(".db_error").html(result.message);
          }
          
        },
        error : function(xhr)
        {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
          console.log(xhr.responseText);
        }
      });
    });
  }

  function clearForm()
  {
      $("input").val('');
      $("input[name='delivery_time']").val('00:00');
  }

</script>

@endsection
