@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>LIST Appointment User : </h2>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container act-tel-tab">
  <div class="input-group col-lg-4">
      <input type="text" class="form-control-lg col-lg-10 search-box" placeholder="Find a campaign by a name" >
      <span class="input-group-append">
        <div class="btn search-icon">
            <span class="icon-search"></span>
        </div>
      </span>
  </div> 
</div>

<div class="container act-tel-apt">
    <table class="table table-bordered mt-4">
      <thead class="bg-dashboard">
        <tr>
          <th colspan="1" class="text-center">No Order</th>
          <th class="text-center">Date Appointment</th>
          <th class="text-center">Name Contact</th>
          <th class="text-center">WA Contact</th>
          <th colspan="2" class="text-center">Action</th>
        </tr>
      </thead>

      <tbody>
        <tr>
          <td class="text-center">1</td>
          <td class="text-center">18 Maret 2020</td>
          <td class="text-center">Ali Mahrus</td>
          <td class="text-center">6281111111</td>
          <td class="text-center"><a class="icon-edit"></a></td>
          <td class="text-center"><a class="icon-cancel"></a></td>
        </tr> 
      </tbody>
    </table>
</div>
@endsection
