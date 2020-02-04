@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>CREATE CAMPAIGN</h2>
  </div>

  <div class="clearfix"></div>
</div>

<!-- NUMBER -->
<div class="container act-tel-campaign">
  <form>
      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Name :</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" />
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Campaign :</label>
        <div class="col-sm-9">
          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign" id="inlineRadio1" value="event" checked>
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio1">Event</label>
          </div>

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign" id="inlineRadio2" value="auto">
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio2">Auto Responder</label>
          </div>

          <div class="form-check form-check-inline">
            <label class="custom-radio">
              <input class="form-check-input" type="radio" name="campaign" id="inlineRadio3" value="broadcast">
              <span class="checkmark"></span>
            </label>
            <label class="form-check-label" for="inlineRadio3">Broadcast</label>
          </div>
          <!-- -->
        </div>
      </div>

      <div class="form-group row">
        <label class="col-sm-3 col-form-label">Select List :</label>
        <div class="col-sm-9 relativity">
           <select class="custom-select-campaign form-control">
              <option>Type</option>
              <option>...</option>
           </select>
           <span class="icon-carret-down-circle"></span>
        </div>
      </div>

      <div class="form-group row event-time">
        <label class="col-sm-3 col-form-label">Event Time :</label>
        <div class="col-sm-9 relativity">
          <input type="text" name="event_time" class="form-control custom-select-campaign" />
          <span class="icon-calendar"></span>
        </div>
      </div>

      <div class="form-group row reminder">
        <label class="col-sm-3 col-form-label">Select Reminder :</label>
        <div class="col-sm-9 relativity">
           <select name="day_reminder" class="custom-select-campaign form-control">
              <option>H-1</option>
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
        <button type="submit" class="btn btn-custom">Create</button>
      </div>

  </form>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    displayOption();
  });

  function displayOption(){
    $("input[name=campaign]").change(function(){
        var val = $(this).val();

        if(val == 'event')
        {
          $("input[name=event_time]").prop('disabled',false);
          $("input[name=day_reminder]").prop('disabled',false);
          $(".event-time").show();
          $(".reminder").show();
        }
        else if(val == 'auto'){
          $("input[name=event_time]").prop('disabled',true);
          $(".event-time").hide();
          $("input[name=day_reminder]").prop('disabled',false);
          $(".reminder").show();
        }
        else {
          $("input[name=event_time]").prop('disabled',true);
          $("input[name=day_reminder]").prop('disabled',true);
          $(".event-time").hide();
          $(".reminder").hide();
        }

    });
  }
</script>
@endsection
