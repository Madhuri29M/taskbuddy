@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <h1>
        {{trans('panel.user_detail')}}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('panel.home')}}</a></li>
        <li><a href="{{route('user.index')}}">{{trans('panel.users_list')}}</a></li>
        <li class="active">{{trans('panel.view')}}</li>
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
                              <th>{{trans('panel.name')}}</th>
                              <td>{{$user->name}}</td>
                            </tr>
                            <tr>
                              <th>{{trans('panel.email')}}</th>
                              <td>{{$user->email}}</td>
                            </tr>
                            <tr>
                              <th>{{trans('panel.mobile_number')}}</th>
                              <td>{{$user->phone_number}}</td>
                            </tr>
                            <tr>
                              <th>{{trans('panel.status')}}</th>
                              <td>{{$user->status}}</td>
                            </tr>
                            <tr>
                              <th>{{trans('panel.verified')}}</th>
                              <td>{{$user->verified == 0 ? 'Verified' : 'Not Verified'}}</td>
                            </tr>
                            <tr>
                              <th>{{trans('panel.created_at')}}</th>
                              <td>{{$user->created_at}}</td>
                               @if($user->profile_image != null)
                            <tr>
                              <th>{{trans('panel.profile_image')}}</th>
                              <td><img src="{{asset($user->profile_image)}}" height='200px'></td>
                            </tr>
                            @endif
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