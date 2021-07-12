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

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            @if (session('status'))
              <div class="alert alert-success" role="alert">
                  {{ session('status') }}
              </div>
            @endif
            <div class="form-group has-feedback">
                <label for="email" class="col-form-label text-md-right content-label" >{{ trans('auth.email') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @error('email')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
            </div>
            <div class="form-group row mb-0">
                <div class="col-md-6 offset-md-4">
                    <button type="submit" class="btn btn-primary">
                        {{ trans('auth.send_reset_pass_link') }}
                    </button>
                </div>
            </div>
            <!-- /.col -->
        </form>
               
</div>
@endsection
