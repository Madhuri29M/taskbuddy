@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
       {{trans('cities.details')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
      <li><a href="{{route('cities.index')}}">{{ trans('cities.plural')}}</a></li>
      <li class="active"> {{trans('cities.show')}}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-body">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <table class="table">
                    <tbody>
                      <tr>
                        <th> {{trans('cities.name')}}</th>
                        <td>{{$city->name}}</td>
                        <tr>
                        <th> {{trans('cities.state_name')}}</th>
                        <td>{{$city->state->name}}</td>
                        <tr>
                        <th> {{trans('cities.country_name')}}</th>
                        <td>{{$city->state->country->name}}</td>
                      </tr>
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
</tr>
@endsection

