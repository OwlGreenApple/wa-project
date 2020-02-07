@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
          
           <div class="act-tel-register bg-dashboard wrapper">

                @if(session('error_phone'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error_phone') }}
                    </div>
                @endif

                <form class="add-contact" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group text-center">
                      <big>Hello</big>
                    </div>

                    <div class="form-group">
                      <label>Name*</label>
                      <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="Input Your Name" />
                      @error('username')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label>Email*</label>
                       <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Input Your Email">

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                      <label>Handphone Telegram*</label>
                      <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="Input Your Phone" />
                      @error('phone')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                    </div>

                    <div class="form-group">
                      <label>Gender*</label>
                      <div>
                        <div class="form-check form-check-inline">
                          <label class="custom-radio">
                            <input class="form-check-input" type="radio" name="campaign" id="inlineRadio1" value="event" checked>
                            <span class="checkmark"></span>
                          </label>
                          <label class="form-check-label" for="inlineRadio1">Male</label>
                        </div>

                        <div class="form-check form-check-inline">
                          <label class="custom-radio">
                            <input class="form-check-input" type="radio" name="campaign" id="inlineRadio2" value="auto">
                            <span class="checkmark"></span>
                          </label>
                          <label class="form-check-label" for="inlineRadio2">Female</label>
                        </div>

                      </div>
                      <!-- -->
                    </div>

                    <div class="form-group">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="agreement"/>
                            <span class="checkmark-check"></span>
                        </label>
                        <label class="checkbox-left"><sb>I Agree with <a>Terms and Condition</a></sb></label>
                    </div>

                    <div class="text-left">
                      <button type="submit" class="btn btn-custom btn-lg">REGISTER</button>
                    </div>
                </form>

                <hr class="mt-5" />

                <div class="mt-4 mb-3"><sb>Already Have An Account? <a href="{{route('login')}}">Log in Here</a></sb></div>
            <!-- end wrapper -->
           </div>

        </div>
    </div>
</div>
@endsection
