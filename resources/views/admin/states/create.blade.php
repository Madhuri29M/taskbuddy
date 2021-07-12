@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
       {{ trans('states.add_new')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
      <li><a href="{{route('states.index')}}">{{ trans('states.plural')}}</a></li>
      <li class="active">{{ trans('states.add_new')}}</li>
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
            <h3 class="box-title">{{ trans('states.details') }}</h3>
              @can('state-list')
              <ul class="pull-right">
                  <a href="{{route('states.index')}}" class="btn btn-success">
                      <i class="fa fa-arrow-left"></i>
                      {{ trans('common.back') }}
                  </a>
              </ul>
              @endcan
          </div>
          <div class="box-body">
            <form method="POST" id="stateForm" action="{{route('states.store')}}" accept-charset="UTF-8">
              @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="name"> {{ trans('states.country')}}</label>
                      <select name="country_name" class="form-control">
                        <option value="">{{trans('states.select_country')}}</option>
                        @foreach($countries as $country)
                          <option value="{{$country->id}}" @if(old('country_id') == $country->id) selected @endif >{{$country->name}}</option>
                        @endforeach
                      </select>
                      <strong class="help-block">
                        {{ @$errors->first('country_name') }}
                      </strong>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="state_name">{{ trans('states.name') }}</label>
                      <input class="form-control" placeholder="Enter State" required="true" name="state_name" type="text" id="state_name"  value="{{old('name')}}" required>
                      <strong class="help-block">
                        {{ @$errors->first('state_name') }}
                      </strong>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{ trans('common.submit') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
