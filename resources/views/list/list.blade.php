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

      <div align="center">
         @error('listname')
            <div class="alert alert-danger col-md-6" role="alert">
                {{ $message }}
            </div>
         @enderror

        <form method="GET" action="{{url('list-create')}}">
          @csrf
          <div class="form-group">
            <input name="listname" type="text" class="form-control custom-form" placeholder="Your List Name"/>
          </div>

          <div class="form-group">
            <textarea name="autoreply" id="divInput-description-post" class="form-control custom-form text-left" placeholder="Auto Reply Text"></textarea>
          </div>

          <!-- open this on version 2 later
           <div class="input-group form-group">
               <select id="phoneid" name="phoneid" class="form-control custom-select">
                  if(phonenumber->count() > 0)
                    foreach(phonenumber as rows)
                      <option value="rows->id">rows->phone_number</option>
                    endforeach
                  endif
               </select>
            </div> 
          -->

          <div class="text-right">
            <button class="btn btn-custom" href="{{url('lists-create')}}">Create List</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>

<script type="text/javascript">
  $("#divInput-description-post").emojioneArea({
        pickerPosition: "right",
        mainPathFolder : "{{url('')}}",
  });
</script>
@endsection
