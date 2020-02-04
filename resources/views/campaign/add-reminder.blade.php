@extends('layouts.app')

@section('content')

<!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>ADD REMINDER : <color>Test Campaigns</color></h2>
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
        <label class="col-sm-3 col-form-label">Choose Reminder Time :</label>
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
          <th class="text-center" style="width : 200px">Reminder Time</th>
          <th class="text-center">Reminder Messages</th>
          <th class="text-center" style="width : 60px">Edit</th>
          <th class="text-center" style="width : 60px">Delete</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">H-1</td>
          <td>Remindered Message H-1</td>
          <td class="text-center"><a class="icon icon-edit"></a></td>
          <td class="text-center"><a class="icon icon-delete"></a></td>
        </tr>
      </tbody>
    </table>
</div>
@endsection
