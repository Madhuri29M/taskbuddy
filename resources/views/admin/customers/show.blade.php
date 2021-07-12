@extends('layouts.admin.app')

@section('css')
<style>
  .details{padding: 10px; background: #efebeb}
</style>
@endsection

@section('content')
<div class="content-wrapper"> 
  <section class="content-header">
    <h1>
      {{ trans('customers.show') }}
    </h1>
    <ol class="breadcrumb">
      <li>
        <a href="{{route('home')}}">
          <i class="fa fa-dashboard"></i>{{ trans('common.home') }}
        </a>
      </li>
      <li>
        <a href="{{route('customers.index')}}">{{trans('customers.singular')}}</a>
      </li>
      <li class="active">
        {{ trans('customers.show') }}
      </li>
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
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">{{ trans('customers.details') }}</h3>
            <ul class="pull-right">
                <!-- <a href="{{route('customers.index')}}" class="btn btn-success">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a> -->
                <a href="{{ url()->previous() }}" class="btn btn-success">
                    <i class="fa fa-arrow-left"></i>
                    {{ trans('common.back') }}
                </a>
            </ul>
          </div>
          <div class="box-body">            
              <div class="model-body">
                <div class="row">
                  <div class="col-md-6">  
                    <div class="form-group">
                      <label for="name" class="content-label">{{trans('customers.name')}}</label>
                       <p class="details">{{$customers->first_name}} {{$customers->last_name}}  </p> 
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email" class="content-label">{{trans('customers.email')}}</label>
                      <p class="details">{{$customers->email}}</p>                         
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone_number" class="content-label">{{trans('customers.phone_number')}}</label>
                      <p class="details">{{$customers->mobile_number}}</p>                         
                    </div>
                  </div>
                <!--   <div class="col-md-6">
                    <div class="form-group">
                      <label for="gender" class="content-label">{{trans('customers.gender')}}</label>
                      <p class="details">{{$customers->gender}}</p>                        
                    </div>
                  </div>
                </div> -->
                <!-- <div class="row"> -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="registered_on" class="content-label">{{trans('customers.registered_on')}}</label>
                      <p class="details">{{$customers->registered_on}}</p>                         
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="register_via" class="content-label">{{trans('customers.register_via')}}</label>
                      <p class="details">{{$customers->social_type}}</p>                         
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="status" class="content-label">{{trans('common.status')}}</label>
                      <p class="details">{{$customers->status}}</p>                         
                    </div>
                  </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
