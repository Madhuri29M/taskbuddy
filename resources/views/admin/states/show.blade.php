@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
       {{trans('states.details')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
      <li><a href="{{route('states.index')}}">{{ trans('states.plural')}}</a></li>
      <li class="active"> {{trans('states.show')}}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          
          <div class="box-body">
              <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                      <table class="table">
                        <tbody>
                          <tr>
                            <th> {{trans('states.name')}}</th>
                            <td>{{$state->name}}</td>
                          </tr>
                          <tr>
                            <th> {{trans('states.country_name')}}</th>
                            <td>{{$state->country->name}}</td>
                          </tr>
                        </tbody>
                      </table>
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
