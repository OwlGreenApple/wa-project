@extends('layouts.app')

@section('content')
<link href="{{ asset('assets/css/thankyou.css') }}" rel="stylesheet">


<div class="container konten konten-register">
  <div class="offset-sm-2 col-sm-8">
    <div class="card h-80 card-payment" style="margin-bottom: 50px">
      <div class="card-body">
          <span class="icon-thankyou" style="font-size: 60px;color: #106BC8">
            <i class="fas fa-check-circle"></i>
          </span>
          <h1>Thank You<br> For Your Payment</h1>
          <hr class="orn" style="color: #106BC8">
          <div class="form-group">
          <p class="pg-title">Silahkan, tunggu proses konfirmasi dari admin <br>maksimum 1x24jam kerja. <br>
          <br>
          </p>
          </div>
          <div class="form-group offset-md-2 col-md-8 col-12">
            <a href="{{url('/')}}" class="free-underline">
              <input type="button" class=" btn btn-primary bsub btn-block" value="Kembali ke Dashboard" style="margin-top:-10px;" />
            </a>
          </div>
        </div>
      </div>
    </div>
</div>

@endsection