@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
<section class="content-header">
  <h1>
    {{trans('role.add_new')}}
    <!-- <small>advanced tables</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
    <li><a href="#">{{trans('role.plural')}}</a></li>
    <li class="active">{{trans('role.add_new')}}</li>
  </ol>
</section>
<section class="content">
    @if (count($errors) > 0)
        <div class="alert alert-danger">
          <strong>{{trans('role.whoops!')}}</strong> {{trans('role.some_problems_with_input')}}
            <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans('role.details') }}</h3>
                    @can('role-list')
                        <ul class="pull-right">
                            <a href="{{route('roles.index')}}" class="btn btn-danger">
                                <i class="fa fa-arrow-left"></i>
                                {{ trans('common.back') }}
                            </a>
                        </ul>
                    @endcan
                </div>
                <div class="box-body">
                    {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="usr">{{trans('role.name')}}</label> 
                                        <small>{{trans('role.used_for_assignment')}}</small>
                                        {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}
                                    </div>
                                </div>
                                <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="usr">{{trans('role.role_title')}}</label>
                                        {!! Form::text('title', null, array('placeholder' => 'Title','class' => 'form-control')) !!}
                                    </div>
                                </div> -->
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="usr">{{trans('role.permissions')}}</label>
                                        <br>
                                        @foreach($permission as $value)
                                            <label>{{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                            {{ $value->name }}</label>
                                        <br/>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button id="btn_add_tml" type="submit" class="btn btn-danger btn-fill btn-wd">{{trans('common.submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection