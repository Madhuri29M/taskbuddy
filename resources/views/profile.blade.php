@extends('layouts.admin.app')
@section('content')    
<div class="content-wrapper">                                
    <section class="content-header">
        <h1>
            {{ $user->name}}
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
            <li class="active">{{ trans('users.profile') }}</li>
        </ol>
        <br>
    </section>
    <section class="content">
        @if ($errors->any())
            <div class="alert alert-danger">
                <b>{{trans('common.whoops')}}</b>
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ trans('users.profile')}}</h3>
                    </div>
                    <div class="box-body">
                        <form method="POST" id="profile" action="{{route('update_profile')}}" accept-charset="UTF-8" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="content-lable-astrisk">{{trans('users.full_name')}}</label>
                                            <input class="form-control" placeholder="{{trans('users.name')}}" type="text" name="full_name" value="{{$user->first_name}}" id="full_name" required>
                                            <strong class="help-block">
                                                {{ @$errors->first('name') }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email" class="content-lable-astrisk">{{trans('users.email')}}</label>
                                            <input class="form-control" placeholder="{{trans('users.email')}}" type="text" name="email" value="{{$user->email}}" id="email" required>
                                            <strong class="help-block">
                                              {{ @$errors->first('email') }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name" class="content-lable-astrisk">{{trans('users.register_as')}}</label>
                                            <input class="form-control" placeholder="{{trans('users.register_as')}}" type="text" name="register_as" value="{{trans('auth.'.$user->user_type)}}" id="name" disabled>
                                            <strong class="help-block">
                                                {{ @$errors->first('name') }}
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mobile_number" class="content-lable-astrisk">{{trans('users.mobile_number')}}</label>
                                            <input class="form-control" placeholder="{{trans('users.mobile_number')}}" type="text" name="mobile_number" value="{{$user->mobile_number}}" id="mobile_number" required>
                                            <strong class="help-block">
                                              {{ @$errors->first('mobile_number') }}
                                            </strong>
                                        </div>
                                    </div>
                                </div>         
                                <div class="col-md-16">
                                    <div class="box box-default collapsed-box">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">{{trans('users.change_password')}}</h3>
                                            <div class="box-tools pull-right">
                                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="box-body">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="new_password" class="content-label">{{trans('users.new_password')}}</label>
                                                    <input class="form-control" placeholder="{{trans('users.new_password')}}" type="password" name="new_password" value="{{$user->new_password}}" id="new_password">
                                                    <strong class="help-block">
                                                      {{ @$errors->first('new_password') }}
                                                    </strong>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="confirm_password" class="content-label">{{trans('users.confirm_password')}}</label>
                                                    <input class="form-control" placeholder="{{trans('users.confirm_password')}}" type="password" name="confirm_password" value="{{$user->confirm_password}}" id="confirm_password">
                                                    <strong class="help-block">
                                                      {{ @$errors->first('confirm_password') }}
                                                    </strong>
                                                </div>
                                            </div>
                                            <input type="hidden" name="used_email" value="{{$user->email}}" id="used_email">
                                        </div>
                                    </div>
                                </div>   
                            </div>
                            <div class="modal-footer">
                                <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{trans('common.submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
@section('js')
<script>

   // Send ALERT iF EMAIL CHANGED
    function emailChanged()
    {
       var old_email = document.getElementById('used_email').value;
       var email = document.getElementById('email').value;
      
       if(old_email != email){

        if(confirm('{{trans("users.profile_email_chaged")}}')){

            return true;

        }else{

           return false;
        }


       }else{ 
        return true;
      }
    }

    $(document).ready(function () {
    $('#admin').validate({ 
        rules: {
            name: {
                required: true
            },
            email: {
                required: true,
                email:true
            },
            mobile_number: {
                required:true,
                maxlength:15,
            },
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        errorPlacement: function (error, element) {
            error.insertAfter($(element));
            $(element).closest('.form-group').addClass('has-error');
        }
    });
});
</script>
@endsection
