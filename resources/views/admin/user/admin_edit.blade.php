@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
<section class="content-header">
  <h1>
    Admin Details
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Edit Admin Profile</li>
  </ol>
</section>
    <section class="content">
      <div class="row">
        <div class="col-md-12">
          <div class="box box-warning">
            <div class="box-header with-border">
              <h3 class="box-title">Edit {{$admin->name}} Profile</h3>
              <a href="{{route('home')}}" class="btn btn-success pull-right">
              <i class="fa fa-arrow-left"></i>
              {{ trans('common.back') }}
          </a>
            </div>
            <div class="box-body">
                {!! Form::open(['route' => ['admin_update_profile',$admin->id],'method' => 'PUT','enctype'=>'multipart/form-data']) !!}
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('name',trans('panel.name') ,['class' => 'content-lable-astrisk'])}}
                                    <input type="hidden" name="user_type" value="{{$admin->user_type}}">
                                    {!!Form::text('name', $admin->first_name, ['class' => 'form-control','placeholder'=>trans('panel.name'),'required'=>'true'])!!}
                                    @error('name')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    {{Form::label('email',trans('panel.email'),['class' => 'content-lable-astrisk'])}}
                                    {!!Form::email('email', $admin->email,['class' => 'form-control','placeholder'=>trans('panel.email'),'required'=>'true'])!!}
                                    @error('email')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- <div class="col-md-1">
                                <div class="form-group">
                                  <label for="name"> {{ trans('panel.country')}}</label>
                                  <select name="country_id" class="form-control">
                                    @foreach($countries as $country)
                                      <option value="{{$country->id}}" @if($country->id == $admin->country_id) selected @endif>{{$country->country_code}}</option>
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
                                    {!!Form::text('mobile_number', $admin->mobile_number,['class' => 'form-control','placeholder'=>"Enter User's Mobile Number",'required'=>'true'])!!}
                                    @error('mobile_number')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                        </div>
                        <div class="row">
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                  <label for="name"> {{ trans('panel.select_country')}}</label>
                                  <select name="country_id" class="form-control">
                                    @foreach($countries as $country)
                                      <option value="{{$country->id}}" @if($country->id == $admin->country_id) selected @endif>{{$country->name}}</option>
                                    @endforeach
                                  </select>
                                    @error('country_id')
                                      <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('old_password',trans('panel.old_password'))}}
                                    {!!Form::password('old_password',['class' => 'form-control','placeholder'=>trans('panel.old_password')])!!}
                                    @error('old_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('new_password',trans('panel.new_password'))}}                                    {!!Form::password('new_password',['class' => 'form-control','placeholder'=>trans('panel.new_password')])!!}
                                    @error('new_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {{Form::label('confirm_password',trans('panel.confirm_password'))}}  
                                    {!!Form::password('confirm_password',['class' => 'form-control','placeholder'=>trans('panel.confirm_password')])!!}
                                    @error('confirm_password')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> -->
                        </div>
                        <label>{{ trans('panel.profile_pic') }}</label> 
                        <div class="row">
                            <div class="col-md-12">
                              <img id="preview" src="{{($admin->profile_image != null) ? asset($admin->profile_image) :''}}" alt="preview image" onerror="this.style.opacity='0'" style="max-height: 200px; max-width:500px;">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">       
                                    <input type="file" name="profile_image" placeholder="Choose image"  id="image">
                                    @error('profile_image')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{trans('common.submit')}}</button>
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
