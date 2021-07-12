@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
<section class="content-header">
  <h1>
    {{trans('role.show')}}
    <!-- <small>advanced tables</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
    <li><a href="#">{{trans('role.plural')}}</a></li>
    <li class="active">{{trans('role.show')}}</li>
  </ol>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans('role.details')}}</h3>
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
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usr">{{trans('role.name')}} : </label>
                                    {{ $role->name }}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usr">{{trans('role.permissions')}} : </label>
                                    <br>
                                    @if(!empty($rolePermissions))
                                        @foreach($rolePermissions as $v)
                                            <label class="badge badge-success">{{ $v->name }}</label>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
@endsection