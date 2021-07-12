<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('adminlte.name', 'Laravel') }} @if(@$page_title) - {{$page_title}} @endif</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="icon" href="{{asset('favicon.png')}}" type="image/x-icon">
  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/bower_components/bootstrap/dist-rtl/css/bootstrap.min.css') }} ">
  <?php }else{ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/bower_components/bootstrap/dist/css/bootstrap.min.css') }} ">
  <?php } ?>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('adminAsset/bower_components/font-awesome/css/font-awesome.min.css') }} ">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('adminAsset/bower_components/Ionicons/css/ionicons.min.css') }} ">
  <!-- Select2 -->
  <link rel="stylesheet" href="{{asset('adminAsset/bower_components/select2/dist/css/select2.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/multiselect/multiselect.css') }}">
  <!-- Theme style -->
  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/dist-rtl/css/AdminLTE.css') }} ">
  <?php }else{ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/dist/css/AdminLTE.min.css') }} ">
  <?php } ?>

  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('adminAsset/plugins/iCheck/square/blue.css') }} ">

  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/custom/fonts/dubai_medium.css') }}">
  <?php } ?>   


  <!-- Toaster -->
  <link rel="stylesheet" href="{{asset('css/toastr.min.css')}}" />
  @yield('css')
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    @if (count($errors) > 0)
          <div class="alert alert-danger">
              <strong>{{ trans('auth.error') }}!</strong> {{ trans('auth.input_error') }}<br><br>
              <ul class="list-group">
                  @foreach ($errors->all() as $error)
                      <li class="list-group-item list-group-item-danger">{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
    @endif

    @if (Session::has('message'))
        <div class="alert alert-success">
          {{ Session::get('message') }}
        </div>
    @endif

    @if (Session::has('error_message'))
        <div class="alert alert-danger">
          {{ Session::get('error_message') }}
        </div>
    @endif
    <div class="login-logo">
      <a href="{{ route('home')}}"><img src="{{asset('logo.svg')}}"></a>
    </div>
    @yield('content')
  </div>

<!-- /.login-box -->
<!-- For Multi Select -->
<script src="{{ asset('js/multiselect/multiselect.min.js') }}"></script>
<script>
    document.multiselect('.multiselect1');
</script>

<!-- jQuery 3 -->
<script src="{{ asset('adminAsset/bower_components/jquery/dist/jquery.min.js') }} "></script>
<!-- Bootstrap 3.3.7 -->
<script src="{{ asset('adminAsset/bower_components/bootstrap/dist/js/bootstrap.min.js') }} "></script>
<!-- SlimScroll -->
<script src="{{ asset('adminAsset/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('adminAsset/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- adminAssetLTE App -->
<script src="{{ asset('adminAsset/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('adminAsset/dist/js/demo.js') }}"></script>
<!--Toaster JS-->
<script src="{{asset('js/toastr.min.js')}}"></script>
<script>
  $(document).ready(function () {
    $('.sidebar-menu').tree()
    $('.multiselect-checkbox').next().first().html("{{trans('common.all')}}");
  })
</script>
@yield('js')

</body>
</html>
