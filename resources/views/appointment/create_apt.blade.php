@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>Create For Appointment</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-apt">
  <div id="error_db" class="col-lg-12"></div>
  <form id="save_apt">
      
      <div class="form-group row lists">
        <label class="col-sm-3 col-form-label">Select List :</label>
        <div class="col-sm-9 relativity">
           <select name="list_id" class="custom-select-campaign form-control">
              @if($lists->count() > 0)
                @foreach($lists as $row)
                  <option value="{{$row->id}}">{{$row->label}}</option>
                @endforeach
              @endif
           </select>
           <span class="icon-carret-down-circle"></span>
           <span class="error list_id"></span>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Name Of Appointment :</label>
        <div class="col-sm-9">
          <input type="text" name="name_app" class="form-control" />
          <span class="error name_app"></span>
        </div>
      </div>

      <div class="text-right">
        <button class="btn btn-custom">Create Appointment</button>
      </div>
  </form>

</div>

<script type="text/javascript">

  $(document).ready(function(){
    saveAppointment();
  });

  function saveAppointment()
  {
    $("#save_apt").submit(function(e){
      e.preventDefault();
      var data = $(this).serialize();
      var id;

      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type : 'POST',
          url : '{{url("save-apt")}}',
          data : data,
          dataType : 'json',
          beforeSend: function()
          {
            $('#loader').show();
            $('.div-loading').addClass('background-load');
          },
          success : function(result){

            if(result.success == 0)
            {  
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
                $(".error").show();
                $(".error_db").html(result.message);
                $(".list_id").html(result.list_id);
                $(".name_app").html(result.name_app);
            }
            else
            {
                $(".error").hide();
                id = result.id;
                alert(result.message);
                location.href='{{ url("edit-apt") }}/'+id;
            }
          },
          error : function(xhr,attribute,throwable)
          {
            $('#loader').hide();
            $('.div-loading').removeClass('background-load');
            console.log(xhr.responseText);
          }
      });
      //ajax
    });
  }
</script>
@endsection
