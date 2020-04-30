@extends('layouts.app')

@section('content')

  <!-- TOP SECTION -->
<div class="container act-tel-dashboard">
  <div class="act-tel-dashboard-left">
    <h2>List</h2>
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
          <div class="form-group mt-5">
            <input name="listname" value="@if(session('listname')){{ session('listname') }}@endif" type="text" class="form-control custom-form" placeholder="Your List Name"/>
             @error('listname')
                <span class="error">{{ $message }}</span>
             @enderror
          </div>

          <div class="form-check mt-2">
            <input class="form-check-input" type="radio" name="is_secure" id="standardRadio" value="0" checked>
            <label class="form-check-label" for="standardRadio">
              Standard 
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="is_secure" id="secureRadio" value="1">
            <label class="form-check-label" for="secureRadio">
              Secure Auto Reply
            </label>
          </div>

          <div class="form-group mt-3">
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
  
  var tempText;
  $(document).ready(function(){
    $("body").on("click","#secureRadio",function(){
      tempText = $("#divInput-description-post").emojioneArea()[0].emojioneArea.getText();
      
      $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText('Hi [NAME], \n Terima Kasih sudah mendaftar \n Langkah selanjutnya adalah : \n - Untuk menerima pesan klik > [START] \n - Untuk Unsubs klik > [UNSUBS]');
    });
    $("body").on("click","#standardRadio",function(){
      $("#divInput-description-post").emojioneArea()[0].emojioneArea.setText(tempText);
    });
  });



</script>
@endsection
