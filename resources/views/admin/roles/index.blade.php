@extends('layouts.admin.app')
@section('css')
<link rel="stylesheet" href="{{asset('admin/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
  td form{
    display: inline; 
  }
</style>
@endsection    
@section('content')
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    {{trans('role.heading')}}
    <!-- <small>advanced tables</small> -->
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
    <li class="active">{{trans('role.plural')}}</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">{{trans('role.title')}}</h3>
          @can('role-list')
            <h3 class="box-title pull-right">
              <a href="{{ route('roles.create',basename(Request::url())) }}" class="btn btn-danger pull-right">
                {{trans('role.add_new')}}
              </a>
            </h3>
          @endcan
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="example2" class="table table-bordered table-hover">
            <thead>
              <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('role.name')}}</th>
                <!-- <th>{{trans('role.role_title')}}</th> -->
                <th>{{trans('common.action')}}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($roles as $key => $role)
                <tr>
                  <td>{{++$key}}</td>
                  <td>{{ $role->name }}</td>
                  <!-- <td>{{ $role->title }}</td> -->
                  <td>
                    <a class="btn btn-danger" href="{{ route('roles.show',$role->id) }}">{{trans('common.show')}}</a>
                    @can('role-edit')
                        <a class="btn btn-primary" href="{{ route('roles.edit',$role->id) }}">{{trans('common.edit')}}</a>
                    @endcan
                    @can('role-delete')
                      {!! Form::open(['method' => 'DELETE','route' => ['roles.destroy', $role->id],'style'=>'display:inline']) !!}
                        <button class="btn btn-danger" type="submit">
                          <span class=""><i class="fa fa-close"></i> {{trans('common.delete')}}</span>
                        </button>
                      {!! Form::close() !!}
                    @endcan
                  </td>
                </tr>
              @endforeach  
            </tbody>
            <tfoot>
              <tr>
                <th>{{trans('common.id')}}</th>
                <th>{{trans('role.name')}}</th>
                <!-- <th>{{trans('role.role_title')}}</th> -->
                <th>{{trans('common.action')}}</th>
              </tr>
            </tfoot>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->
</section>
<!-- /.content -->
</div>
@endsection
@section('js')
<script type="text/javascript">
  $('#example2').DataTable({
                'paging'      : true,
                'lengthChange': true,
                'searching'   : true,
                'ordering'    : true,
                'info'        : true,
                'autoWidth'   : true
              })
</script>
@endsection