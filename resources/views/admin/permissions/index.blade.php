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
      {{trans('permission.heading')}}
      <!-- <small>advanced tables</small> -->
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li class="active">{{trans('permission.plural')}}</li>
    </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{trans('permission.title')}}</h3>
            @can('permission-create')
              <h3 class="box-title pull-right">
                <a href="{{ route('permissions.create',basename(Request::url())) }}" class="btn btn-danger pull-right">
                  Add New Permission
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
                  <th>{{trans('permission.name')}}</th>
                  @can('role-delete')
                  <th>{{trans('common.action')}}</th>
                  @endcan
                </tr>
              </thead>
              <tbody>
                @foreach($permissions as $key => $permission)
                  <tr>
                    <td>{{++$key}}</td>
                    <td>{{ $permission->name }}</td>
                    @can('permission-delete')
                    <td>
                        {!! Form::open(['method' => 'DELETE','route' => ['permissions.destroy', $permission->id],'style'=>'display:inline']) !!}
                          <button class="badge bg-red" type="submit">
                            <span class=""><i class="fa fa-close"></i> {{trans('common.delete')}}</span>
                          </button>
                        {!! Form::close() !!}
                    </td>
                    @endcan
                  </tr>
                @endforeach  
              </tbody>
              <tfoot>
              <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('permission.name')}}</th>
                  @can('role-delete')
                  <th>{{trans('common.action')}}</th>
                  @endcan
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