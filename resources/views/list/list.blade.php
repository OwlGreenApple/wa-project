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
          
          <div class="form-group">
            <div class="row">
              <div class="col-lg-11">
                <input name="groupname" value="@if(session('groupname')){{ session('groupname') }}@endif" type="text" class="form-control custom-form" placeholder="Telegram Group Name"/>
              </div>
              <div class="col-lg-1 pad-fix text-left">
                <a id="fancybox-modal"><i class="fa fa-question-circle fa-2x mt-5" aria-hidden="true"></i></a>
              </div>
            </div>
             @error('groupname')
                <span class="error">{{ $message }}</span>
             @enderror
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
            caption : 'First caption'
          }
        },
        {
          src  : '{{ asset("assets/hint/hint-2.png") }}',
          opts : {
            caption : 'Second caption'
          }
        }
      ]);
    });
  }
</script>
@endsection
