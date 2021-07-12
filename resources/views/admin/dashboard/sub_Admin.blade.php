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
<!-- adminAssetLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{ asset('adminAsset/dist/js/pages/dashboard2.js') }}"></script> -->
<!-- adminAssetLTE for demo purposes -->
<script src="{{ asset('adminAsset/dist/js/demo.js') }}"></script>
@endsection

@section('css')
<!-- <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css') }}"> -->
@endsection
