@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="act-tel-login bg-dashboard wrapper">
               <form class="add-contact" method="POST" action="{{ route('login') }}">
                  @csrf
                   <div class="form-group text-center">
                      <big>Hello</big>
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
                      <label>Password *</label>
                       <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Input Your Password">
                      
                       @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                       @enderror
                    </div>

                    <div class="form-group mt-5">
                        <label class="custom-checkbox">
                            <input type="checkbox" name="agreement"/>
                            <span class="checkmark-check"></span>
                        </label>
                        <label class="checkbox-left"><sb>I Agree with <a>Terms and Condition</a></sb></label>
                    </div>

                    <div class="text-left">
                      <button type="submit" class="btn btn-custom btn-lg">LOG IN</button>
                    </div>
               </form>

               <hr class="mt-5" />

              <div class="mt-4 mb-3"><sb>Need a Activtele account? <a>Register Here</a></sb></div>
            <!-- end wrapper -->
            </div>

        </div>
    </div>
</div>
@endsection
