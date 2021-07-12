@extends('layouts.admin')
@section('content')
<div class="content-wrapper">
<section class="content-header">
  <h1>
    User Details
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Edit User Profile</li>
  </ol>
</section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Edit {{$user->name}}'s Profile</h3>
            </div>
            <div class="box-body">
                {!! Form::open(['route' => ['user.update',$user->id],'method' => 'PUT','enctype'=>'multipart/form-data']) !!}
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('name', 'Name')}}
                                    <input type="hidden" name="user_type" value="{{$user->user_type}}">
                                    {!!Form::text('name', $user->name,['class' => 'form-control','placeholder'=>"Enter User's Name",'required'=>'true'])!!}
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('email', 'Email')}}
                                    {!!Form::email('email', $user->email,['class' => 'form-control','placeholder'=>"Enter User's Email",'required'=>'true'])!!}
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                  <label for="name"> {{ trans('panel.country')}}</label>
                                  <select name="country_id" class="form-control">
                                    @foreach($countries as $country)
                                      <option value="{{$country->id}}" @if($country->id == $user->country_id) selected @endif>{{$country->country_code}}</option>
                                    @endforeach
                                  </select>
                                    @error('country_id')
                                      <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div class="form-group">
                                    {{Form::label('mobile_number', 'Mobile Number')}}
                                    {!!Form::text('mobile_number', $user->mobile_number,['class' => 'form-control','placeholder'=>"Enter User's Mobile Number",'required'=>'true'])!!}
                                    @error('mobile_number')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                  <label for="name"> {{ trans('panel.select_country')}}</label>
                                  <select name="country_id" class="form-control">
                                    @foreach($countries as $country)
                                      <option value="{{$country->id}}" @if($country->id == $user->country_id) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                  </select>
                                    @error('country_id')
                                      <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('old_password', 'Old Password')}}
                                    {!!Form::text('old_password', old('old_password'),['class' => 'form-control','placeholder'=>"Enter Old Password"])!!}
                                    @error('old_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('new_password', 'New Password')}}
                                    {!!Form::text('new_password', old('new_password'),['class' => 'form-control','placeholder'=>"Enter New Password"])!!}
                                    @error('new_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('confirm_password', 'Confirm Password')}}
                                    {!!Form::text('confirm_password', old('confirm_password'),['class' => 'form-control','placeholder'=>"Confirm New Password"])!!}
                                    @error('confirm_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <label>{{ trans('panel.profile_pic') }}</label> 
                        <div class="row">
                            <div class="col-md-12">
                              <img id="preview" src="{{($user->profile_picture != null) ? \Storage::disk('s3')->url($user->profile_picture) :''}}" alt="preview image" style="max-height: 200px; max-width:500px;">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                   
                                    <input type="file" name="profile_picture" placeholder="Choose image" id="image">
                                    @error('profile_picture')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">Submit</button>
                    </div>
                {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </section>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function () {

      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
 
        $('#image').change(function(){
          
            let reader = new FileReader();
            reader.onload = (e) => { 
              $('#preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
 
        });
});
</script>
@endsection
