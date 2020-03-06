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
        @if(session('error_number'))
          <div class="alert alert-danger col-lg-6">{{ session('error_number') }}</div>
        @endif

        <form method="GET" action="{{url('list-create')}}">
          @csrf
          <div class="form-group">
            <input name="listname" value="@if(session('listname')){{ session('listname') }}@endif" type="text" class="form-control custom-form" placeholder="Your List Name"/>
             @error('listname')
                <span class="error">{{ $message }}</span>
             @enderror
          </div>


          <div class="form-group">
            <textarea name="autoreply" id="divInput-description-post" class="form-control custom-form text-left" placeholder="Auto Reply Text">@if(session('autoreply')){{ session('autoreply') }}@endif</textarea>
          </div>

          <div class="text-right">
            <button class="btn btn-custom">Create List</button>
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
                
  $(function(){
      fancyboxModal();
  });

  function fancyboxModal()
  {
    $('#fancybox-modal').on('click', function() {
      $.fancybox.open([
        {
          src  : '{{ asset("assets/hint/hint-1.png") }}',
          opts : {
            caption : 'Step 1'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-2.png") }}',
          opts : {
            caption : 'Step 2'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-3.png") }}',
          opts : {
            caption : 'Step 3'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-4.png") }}',
          opts : {
            caption : 'Step 4'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-5.png") }}',
          opts : {
            caption : 'Step 5'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-6.png") }}',
          opts : {
            caption : 'Step 6'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-7.png") }}',
          opts : {
            caption : 'Step 7'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-8.png") }}',
          opts : {
            caption : 'Last Step'
          }
        },
      ]);
    });
  }
</script>
@endsection
