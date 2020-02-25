@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>ADD MESSAGE : <color>Test Campaigns</color></h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-campaign">
  <form>
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Type Campaign :</label>
        <div class="col-sm-9 py-2">
          <strong>Event</strong>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Choose Message Time :</label>
        <div class="col-sm-9 relativity">
           <select class="custom-select-campaign form-control">
              <option>H-3</option>
              <option>...</option>
           </select>
           <span class="icon-carret-down-circle"></span>
        </div>
      </div>

       <div class="form-group row">
        <label class="col-sm-3 col-form-label">Time to send Message :</label>
        <div class="col-sm-9 relativity">
          <input type="text" class="form-control" value="00:00" />
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Message :</label>
        <div class="col-sm-9">
          <textarea class="form-control"></textarea>
        </div>
      </div>

      <div class="text-right">
        <button type="submit" class="btn btn-custom">Save</button>
      </div>

  </form>
</div>

<!-- Table -->
<div class="container act-tel-campaign">
    <table class="table table-bordered mt-4">
      <thead class="bg-dashboard">
        <tr>
          <th class="text-center" style="width : 100px">Reminder Time</th>
          <th class="text-center" style="width : 100px">Time Sending</th>
          <th class="text-center">Reminder Messages</th>
          <th class="text-center" style="width : 60px">Edit</th>
          <th class="text-center" style="width : 60px">Delete</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">H-1</td>
          <td class="text-center">00:00</td>
          <td>Remindered Message H-1</td>
          <td class="text-center"><a class="icon icon-edit"></a></td>
          <td class="text-center"><a class="icon icon-delete"></a></td>
        </tr>
      </tbody>
    </table>
</div>

<script type="text/javascript">
  function saveSubscriber(){
    $("#addcustomer").submit(function(e){
        e.preventDefault();
        var data = $(this).serialize();
        //$("#submit").html('<img src="{{asset('assets/css/loading.gif')}}"/>');
        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
            type : "POST",
            url : "{{ route('savesubscriber') }}",
            data : data,
            beforeSend: function()
            {
              $('#loader').show();
              $('.div-loading').addClass('background-load');
            },
            success : function(result){
              $('#loader').hide();
              $('.div-loading').removeClass('background-load');

              if(result.success == true){
                  $(".modal-body > p").text(result.message);
                  alert('Your data has stored!');
                  //getModal();
                  //setTimeout(function(){location.href= result.wa_link} , 1000);   
                  clearField();
              } 
              else {
                  $(".error").fadeIn('fast');
                  $(".name").text(result.name);
                  $(".main").text(result.main);
                  $(".email").text(result.email);
                  $(".phone").text(result.phone);
                  $(".phone").text(result.usertel);
                  $(".captcha").text(result.captcha);
                  $(".error_list").text(result.list);

                  if(result.message !== undefined){
                       $(".error_message").html('<div class="alert alert-danger text-center">'+result.message+'</div>');
                  }
                  $.each(result.data, function(key, value) {
                      $("."+key).text(value);
                  })

                  $(".error").delay(2000).fadeOut(5000);
              }
            }
        });
        /*end ajax*/
    });
  }

  $(document).ready(function() {
  });
  
</script>  
@endsection
