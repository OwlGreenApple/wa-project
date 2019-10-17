@extends('layouts.app')

@section('content')

<!-- navbar -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <nav class="navbar navbar-expand-sm bg-primary navbar-dark">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="{{route('home')}}">Broadcast</a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
</div>
<!- end navbar -->

<!-- -->
<div class="container mb-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><b>Scan Your Device Here</b></div>
                <div class="col-md-12 mt-2"><h5>Please scan within approximately 20 seconds until page reload</h5></div>
                <div class="card-body">
                    @if (session('deviceid'))
                        {!! $qrcode->getScanBarcodeAuthorize(session('deviceid')) !!}
                    @endif
                </div>

            </div>
        </div>
    </div>
<!-- end container -->
</div>

<script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){checkAuthorize('{{session("deviceid")}}')},20000);;
    });

    function checkAuthorize(id)
    {
        var current_status = 'new';
         $.ajax({
            type : 'GET',
            url : '{{url("devicestatus")}}/'+id,
            dataType : 'json',
            success : function(result)
            {
                if(result.status !== current_status)
                {
                    /* this is true (status changed) */
                    location.href='{{route("devices")}}';
                }
                else
                {
                    /* this is false (status won't changed) */
                    alert('Your devices authorized but not inserted due your device is not new device')
                    location.href='{{route("devices")}}';
                }
            }
        })
        
    }
</script>
@endsection
