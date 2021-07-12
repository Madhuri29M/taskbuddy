@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">  
  <section class="content-header">
    <h1>
     {{trans('countries.update')}} {{$country->name}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
      <li><a href="{{route('country.index')}}">{{trans('countries.plural')}}</a></li>
      <li class="active">{{trans('common.edit')}} {{$country->name}}</li>
    </ol>
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
            <!-- <h3 class="box-title">Edit {{$country->name}}</h3> -->
              <h3 class="box-title">{{ trans('countries.details') }}</h3>
              @can('country-list')
              <ul class="pull-right">
                  <a href="{{route('country.index')}}" class="btn btn-success">
                      <i class="fa fa-arrow-left"></i>
                      {{ trans('common.back') }}
                  </a>
              </ul>
              @endcan
          </div>
          <div class="box-body">
              {!! Form::open(['route' => ['country.update',$country->id],'method' => 'PUT','id'=>'country']) !!}
                  {{csrf_field()}}
                  <div class="modal-body">
                      <div class="row">
                          <div class="col-md-4">
                              <div class="form-group @error('country_name') ? has-error : ''  @enderror">
                                  {{Form::label('name', trans('countries.country_name'))}}
                                  {!!Form::text('country_name', $country->name,['class' => 'form-control','placeholder'=>trans('countries.country_name'),'required'=>'true'])!!}
                                  @error('country_name')
                                      <div class="help-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>
                          <div class="col-md-4">
                              <div class="form-group @error('country_code') ? has-error : ''  @enderror">
                                  {{Form::label('country_code',trans('countries.country_code'))}}
                                  {!!Form::text('country_code',$country->country_code,['class' => 'form-control','placeholder'=>trans('countries.country_code'),'required'=>'true'])!!}
                                  @error('country_code')
                                      <div class="help-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>
                          <!-- <div class="col-md-4">
                              <div class="form-group @error('sort_name') ? has-error : ''  @enderror">
                                  {{Form::label('sort_name',trans('countries.sort_name'))}}
                                  {!!Form::text('sort_name', $country->sort_name,['class' => 'form-control','placeholder'=>trans('countries.sort_name'),'required'=>'true'])!!}
                                  @error('sort_name')
                                      <div class="help-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div> -->
                      </div>
                      <!-- <div class="row">
                          <div class="col-md-4">
                              <div class="form-group @error('status') ? has-error : ''  @enderror">
                                  {{Form::label('status', trans('common.status'))}}
                                  {!! Form::select('status', ['active' => 'Active','inactive' => 'Inactive'],$country->status , ['class' => 'form-control']) !!}
                                  @error('status')
                                      <div class="help-block">{{ $message }}</div>
                                  @enderror
                              </div>
                          </div>
                      </div> -->
                  </div>
                  <div class="modal-footer">
                      <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{ trans('common.submit') }}</button>
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
    $('#country').validate({ 
        rules: {
            country_name: {
                required: true,
                // lettersonly : true,
                // alphanumeric : true
            },
            sort_name: {
                required: true,
                // lettersonly : true,
                // alphanumeric : true
            },
            // country_code: {
            //     required: true,
            //     digits: true
            // },
            status: {
                required: true,
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


