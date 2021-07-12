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
      <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title">{{trans('dashboard.booking_chart')}}</h3>
              </div>
              <div class="box-body">
                <div class="chart">
                  <canvas id="bookings_chart" style="width: 100%"></canvas>
                </div>
              </div>
            </div>
        </div>
        
        <div class="col-md-6 col-sm-6 col-xs-12">
            <div class="box box-success">
              <div class="box-header with-border">
                <h3 class="box-title">{{trans('dashboard.artists_chart')}}</h3>
              </div>
              <div class="box-body">
                <div class="chart">                  
                  <canvas id="customer_chart" style="width: 100%"></canvas>
                </div>
              </div>
            </div>
        </div>
        
      </div>

      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <a class="dash_statistics_link" >
              <div class="info-box">
                <span class="info-box-icon bg-green">
                  <i class="fa fa-calendar" aria-hidden="true"></i>
                </span>

                <div class="info-box-content">
                  <span class="info-box-text" title="{{trans('dashboard.today_appointments')}}">{{trans('dashboard.today_appointments')}}</span>
                  <span class="info-box-number">{{$response->today_appointments}}</span>
                </div>
                <!-- /.info-box-content -->
              </div>
            </a>
            <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <a class="dash_statistics_link">
            <div class="info-box">
              <span class="info-box-icon bg-red">
                <i class="fa fa-calendar-times-o" aria-hidden="true"></i>
              </span>

              <div class="info-box-content">
                <span class="info-box-text" title="{{trans('dashboard.cancelled_appointments')}}">{{trans('dashboard.cancelled_appointments')}}</span>
                <span class="info-box-number">{{$response->cancelled_appointments}}</span>
              </div>
              <!-- /.info-box-content -->
            </div>
          </a>
          <!-- /.info-box -->
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
          <a class="dash_statistics_link">
            <div class="info-box">
              <span class="info-box-icon bg-blue">
                <i class="fa fa-group" aria-hidden="true"></i>
              </span>

              <div class="info-box-content">
                <span class="info-box-text" title="{{trans('dashboard.walkin_customers')}}">{{trans('dashboard.walkin_customers')}}</span>
                <span class="info-box-number">{{$response->walkin_appointments}}</span>
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
<!-- adminAssetLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="{{ asset('adminAsset/dist/js/pages/dashboard2.js') }}"></script> -->
<!-- adminAssetLTE for demo purposes -->
<!-- <script src="{{ asset('adminAsset/dist/js/demo.js') }}"></script> -->
@php
  $employee_names = [];
  $employee_bookings_counts = [];
  foreach($response->employees as $emp) {
    array_push($employee_names,$emp['name']);  
    array_push($employee_bookings_counts,$emp['total_count']);  
  }
  $employee_names = json_encode($employee_names);
  $employee_bookings_counts = json_encode($employee_bookings_counts);
@endphp
<script type="text/javascript">
  var bookings_chart = new Chart($("#bookings_chart"), {
    type: 'bar',
    data: {
      labels: [
                "{{trans('common.months.'.date('n', strtotime($response->graph[0]->date)))}} {{date('d', strtotime($response->graph[0]->date))}}", 
                "{{trans('common.months.'.date('n', strtotime($response->graph[1]->date)))}}  {{date('d', strtotime($response->graph[1]->date))}}", 
                "{{trans('common.months.'.date('n', strtotime($response->graph[2]->date)))}} {{date('d', strtotime($response->graph[2]->date))}}", 
                "{{trans('common.months.'.date('n', strtotime($response->graph[3]->date)))}} {{date('d', strtotime($response->graph[3]->date))}}", 
                "{{trans('common.months.'.date('n', strtotime($response->graph[4]->date)))}} {{date('d', strtotime($response->graph[4]->date))}}", 
                "{{trans('common.months.'.date('n', strtotime($response->graph[5]->date)))}} {{date('d', strtotime($response->graph[5]->date))}}", 
                "{{trans('common.months.'.date('n', strtotime($response->graph[6]->date)))}} {{date('d', strtotime($response->graph[6]->date))}}"
              ],
      datasets: [{
        label: '{{trans("bookings.plural")}}',
        data: ['{{$response->graph[0]->count}}', '{{$response->graph[1]->count}}', '{{$response->graph[2]->count}}', '{{$response->graph[3]->count}}', '{{$response->graph[4]->count}}', '{{$response->graph[5]->count}}', '{{$response->graph[6]->count}}'],
        backgroundColor: "#D16F8A",
        hoverBackgroundColor: '#D16F8A',
      }]
    },
    options: {
        scales: {
            xAxes: [{
                ticks: {
                    min: 0 // Edit the value according to what you need
                }
            }],
            yAxes: [{
                stacked: true
            }]
        }
    }
  });

  if($('#customer_chart')){
    var customer_chart = new Chart($("#customer_chart"), {
      type: 'horizontalBar',
      data: {
        labels: <?=$employee_names?>,
        datasets: [{
          label: '{{trans("bookings.plural")}}',
          data: <?=$employee_bookings_counts?>,
          backgroundColor: "#D16F8A",
          hoverBackgroundColor: '#D16F8A',
        }],
      },
      options: {
          scales: {
              xAxes: [{
                  ticks: {
                       // Edit the value according to what you need
                  }
              }],
              yAxes: [{
                  stacked: true
              }]
          }
      }
    });
  }
</script>
@endsection

@section('css')
<!-- <link rel="stylesheet" href="{{ asset('admin/dist/css/skins/_all-skins.min.css') }}"> -->
@endsection
