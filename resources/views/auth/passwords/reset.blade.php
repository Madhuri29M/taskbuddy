@extends('layouts.app')
@section('css')
<style type="text/css">
    .content-label::after {
      content: "*";
      color: red;
    } 
</style>  
@endsection
@section('content')
<div class="login-box-body">
    <p class="login-box-msg">{{trans('auth.reset_password')}}</p>
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <div class="form-group has-feedback">
                <input type="hidden" name="token" value="{{ $token }}">
                <label for="email" class="col-form-label text-md-right content-label">{{ trans('auth.email') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @error('email')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password" class="col-form-label text-md-right content-label">{{ trans('auth.password') }}</label>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password-confirm" class="col-form-label text-md-right content-label">{{ trans('auth.confirm_password') }}</label>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
            </div>
            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ trans('auth.reset_password') }}
                    </button>
                </div>
            </div>
            <!-- /.col -->
        </form>
</div>
@endsection
