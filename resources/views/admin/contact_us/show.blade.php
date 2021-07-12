@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper"> 
  <section class="content-header">
    <h1>
      {{ trans('panel.contact_detail') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('panel.home') }}</a></li>
      <li><a href="{{route('contact_us.index')}}">{{ trans('panel.contact_us') }}</a></li>
      <li class="active">{{ trans('panel.contact_detail') }}</li>
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
                            <th>{{ trans('panel.name') }}</th>
                            <td>{{$contactus->name}}</td>
                          </tr>
                          <tr>
                            <th>{{ trans('panel.type') }}</th>
                            <td>{{$contactus->type}}</td>
                          </tr>
                          <tr>
                            <th>{{ trans('panel.email') }}</th>
                            <td>{{$contactus->email}}</td>
                          </tr>
                          <tr>
                            <th>{{ trans('panel.mobile_number') }}</th>
                            <td>{{$contactus->mobile_number}}</td>
                          </tr>
                          <tr>
                            <th>{{ trans('panel.description') }}</th>
                            <td>{{$contactus->description}}</td>
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
