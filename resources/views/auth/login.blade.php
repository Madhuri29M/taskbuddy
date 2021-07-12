@extends('layouts.app')

@section('content')
  <div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group has-feedback">
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{ __('E-Mail Address') }}*">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group has-feedback">
            <input type="password"  class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}*">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
      <div class="row">
        <div class="col-xs-8">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">
                  {{trans('auth.forgot_password')}}
                </a>
            @endif
        </div>
        
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">{{trans('auth.sign_in')}}</button>
        </div>
            <!-- /.col -->
        </div>
    </form>

    <!-- <div class="social-auth-links text-center">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
    </div> -->
    <!-- /.social-auth-links -->
  </div>
@endsection

@section('js')
<!-- iCheck -->
<script src="{{ asset('adminAsset/plugins/iCheck/icheck.min.js') }} "></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
@endsection