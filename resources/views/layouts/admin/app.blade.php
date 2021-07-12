<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title> {{ @$app_settings['app_name'] }} @if(@$page_title) - {{$page_title}} @endif</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="icon" href="#" type="image/x-icon">
  <!-- Bootstrap 3.3.7 -->
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
  <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
  <!-- Theme style -->
  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/dist-rtl/css/AdminLTE.css') }} ">
  <?php }else{ ?>
    <link rel="stylesheet" href="{{ asset('adminAsset/dist/css/AdminLTE.min.css') }} ">
  <?php } ?>

  <link rel="stylesheet" href="{{asset('adminAsset/plugins/datetimepicker/bootstrap-datetimepicker.min.css')}}">

  <!-- <link rel="stylesheet" href="{{ asset('admin/dist/css/AdminLTE.min.css') }} "> -->
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('adminAsset/plugins/iCheck/square/blue.css') }} ">
  <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
      <link rel="stylesheet" href="{{ asset('adminAsset/dist-rtl/css/skins/_all-skins.min.css') }}">
      <link rel="stylesheet" href="{{ asset('adminAsset/dist-rtl/css/bootstrap-rtl.min.css') }}">
      <link rel="stylesheet" href="{{ asset('adminAsset/dist-rtl/css/profile.css') }}">
      <link rel="stylesheet" href="{{ asset('adminAsset/dist-rtl/css/rtl.css') }}">
      <link rel="stylesheet" href="{{ asset('adminAsset/custom/fonts/dubai_medium.css') }}">
  <?php }else{ ?>
      <link rel="stylesheet" href="{{ asset('adminAsset/dist/css/skins/_all-skins.min.css') }}">
  <?php } ?>



<style type="text/css">
.content-lable-astrisk::after {
    content: "*";
    color: red;
}
/* Loader */
#loader {
    left: 0%;
    top: 0%;
    width: 100%;
    height: 100%;
    position: fixed;
    
    opacity: 0.7;
    z-index: 9999999;
}

.overlay__inner {
    
    left: 50%;
    top: 20%;
    width: 10%;
    height: 30%;
    position: relative;
}

.overlay__content {
    left: 50%;
    position: absolute;
    top: 50%;
    background: #3c8dbc;
    transform: translate(-50%, -50%);
    padding: 20%;
}

.spinner {
    width: 75px;
    height: 75px;
    display: inline-block;
    border-width: 2px;
    border-color: rgba(255, 255, 255, 0.05);
    border-top-color: #fff;
    animation: spin 1s infinite linear;
    border-radius: 100%;
    border-style: solid;
}

@keyframes spin {
  100% {
    transform: rotate(360deg);
  }
}
#loader{display: none}
  </style>
  
  <!-- CkEditor -->
  <script src="{{ asset('adminAsset/bower_components/ckeditor/ckeditor.js') }}"></script>

  <!-- Toaster -->
  <link rel="stylesheet" href="{{asset('css/toastr.min.css')}}" />
  <link rel="stylesheet" href="{{asset('adminAsset/custom/developer.css')}}" />
    <link rel="stylesheet" href="{{asset('css/select.dataTables.min.css')}}" />
  <!-- Datatable Checkbox CSS -->
  <link rel="stylesheet" href="{{asset('css/dataTables.checkboxes.css')}}" />
  
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
<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    

  @include('layouts.admin.partials.header')  
  @include('layouts.admin.partials.sidebar')  
  
  
      @yield('content')
  

  @include('layouts.admin.partials.footer')

</div>
<!-- For Multi Select -->
<script src="{{ asset('js/multiselect/multiselect.min.js') }}"></script>
<script>
    document.multiselect('.multiselect1');
</script>

<!-- jQuery 3 -->
<script src="{{ asset('adminAsset/bower_components/jquery/dist/jquery.min.js') }} "></script>
<!-- Bootstrap 3.3.7 -->

<?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl' ){ ?>
    <script src="{{ asset('adminAsset/bower_components/bootstrap/dist-rtl/js/bootstrap.min.js') }}"></script>
<?php }else{ ?>
    <script src="{{ asset('adminAsset/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<?php } ?>

<script src="{{ asset('adminAsset/plugins/datetimepicker/moment-with-locales.js') }}"></script>
<script src="{{ asset('adminAsset/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('adminAsset/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('adminAsset/bower_components/fastclick/lib/fastclick.js') }}"></script>
<!-- adminAssetLTE App -->
<?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl' ){ ?>
    <script src="{{ asset('adminAsset/dist-rtl/js/adminlte.min.js') }}"></script>
<?php }else{ ?>
    <script src="{{ asset('adminAsset/dist/js/adminlte.min.js') }}"></script>
<?php } ?>

<!-- AdminLTE for demo purposes -->
<?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl' ){ ?>
    <script src="{{ asset('adminAsset/dist-rtl/js/demo.js') }}"></script>
<?php }else{ ?>
    <script src="{{ asset('adminAsset/dist/js/demo.js') }}"></script>
<?php } ?>



<!-- DataTables -->
<script src="{{asset('adminAsset/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('js/dataTables.select.min.js')}}"></script>
<!-- DataTables Checkbox -->
<script src="{{asset('js/dataTables.checkboxes.min.js')}}"></script>
<script>
  $(document).ready(function () {
    <?php if(config('app.locales')[config('app.locale')]['dir'] != 'rtl' ){ ?>
        $('.sidebar-menu').tree()
    <?php }?>
  })
</script>
<!--Toaster JS-->
<script src="{{asset('js/toastr.min.js')}}"></script>

<script type="text/javascript">
    @if(Session::has('success'))
      toastr.success("{{ Session::get('success') }}");
    @elseif(Session::has('error'))
      toastr.error("{{ Session::get('error') }}");
    @elseif(Session::has('warning'))
      toastr.warning("{{ Session::get('warning') }}");
    @elseif(Session::has('info'))
      toastr.info("{{ Session::get('info') }}");
    @endif
</script>

<script type="text/javascript"> 
     
  $('.alert-danger').delay(5000).fadeOut();

   function isNumber(evt) {
          var iKeyCode = (evt.which) ? evt.which : evt.keyCode
          if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
              return false;

          return true;
      }  
</script>

<script type="text/javascript"> 
  $(document).ready(function(){ 
    $('.error').delay(5000).fadeOut();
    $('.multiselect-checkbox').next().first().html("{{trans('common.all')}}");
  });

  $('form').submit(function(){
    $('#edit_btn').prop('disabled', true);
  })

  $('.multiselect-input').attr('autocomplete','off');
    
</script>

<script type="text/javascript" src="{{ asset('adminAsset/custom/jquery.validate.min.js') }}"></script>
<script src="{{ asset('adminAsset/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!-- WEB PUSH NOTOFOCTION -->
<script type="text/javascript">
  var csrf_for_noti = '{{csrf_token()}}';
</script>
<script type="text/javascript" src="{{ asset('js/notification.js') }}"></script>
<!-- WEB PUSH NOTOFOCTION -->
<script type="text/javascript">
  var lang_url = '';
  if ('{{config("app.locale")}}' == 'ar'){
    lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
  }
</script>
@yield('js')
</body>
</html>