@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper"> 
  <section class="content-header">
    <h1>
      {{$country->name}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
      <li><a href="{{route('country.index')}}">{{trans('countries.plural')}}</a></li>
      <li class="active">{{trans('countries.show')}}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="nav-tabs-custom">
          <div class="tab-content active">
            <div class="tab-pane active" id="settings">
              <table class="table">
                <tbody>
                  <tr>
                      <th>{{trans('countries.country_name')}}</th>
                      <td>{{$country->name}}</td>
                  </tr>
                  <tr>
                    <th>{{trans('countries.country_code')}}</th>
                    <td>{{$country->country_code}} </td>
                  </tr>
                  <tr>
                    <th>{{trans('countries.flag')}}</th>
                    <td><img src="{{asset($country->flag)}}"> </td>
                  </tr>
                 <!--  <tr>
                    <th>{{trans('countries.country_status')}}</th>
                    <td>{{$country->status}} </td>
                  </tr> -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
