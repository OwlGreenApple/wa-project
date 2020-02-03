@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>DASHBOARD</h2>
  </div>
  <div class="clearfix"></div>
</div>

<div class="container">
  <div class="col-md-12">
    <div class="act-tel-create-list bg-dashboard">
      <h3>Create Your List</h3>

      <div>
        <form>
          <input type="text" class="form-control custom-form" placeholder="Test List 1"/>
          <div class="text-right">
            <button type="submit" class="btn btn-custom">Create List</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

@endsection
