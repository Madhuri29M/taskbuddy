@extends('layouts.admin.app')

@section('content')

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        {{trans('common.dashboard')}}
       
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
        <li class="active">{{trans('common.dashboard')}}</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
          <a class="dash_statistics_link" href="{{route('customers.index')}}">
            <div class="info-box">
              <span class="info-box-icon bg-aqua">
                <i class="fa fa-users" aria-hidden="true"></i>
              </span>

              <div class="info-box-content">
                <span class="info-box-text" title="{{trans('dashboard.total_customers')}}">{{trans('dashboard.total_customers')}}</span>
                <span class="info-box-number">{{$total_customers}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
          </a>
          <!-- /.info-box -->
        </div>
    
        
        
      </div>
      
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

@endsection

@section('js')
<!-- jvectormap  -->
<script src="{{ asset('adminAsset/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('adminAsset/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('adminAsset/bower_components/chart.js/Chart.js') }}"></script>
<!-- adminAssetLTE for demo purposes -->
<script src="{{ asset('adminAsset/dist/js/demo.js') }}"></script>

<script>
  
</script>
@endsection

@section('css')
<!-- <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css') }}"> -->
@endsection
