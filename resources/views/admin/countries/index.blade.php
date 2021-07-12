@extends('layouts.admin.app')
@section('css')
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<style type="text/css">
  td form{
    display: inline; 
  }
</style>
@endsection    
@section('content')
<div class="content-wrapper"> 
  <section class="content-header">
    <h1>
     {{trans('countries.heading')}} 
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
      <li><a href="{{route('country.index')}}">{{trans('countries.title')}}</a></li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{trans('countries.title')}}</h3>
      <!--  @can('country-create')
            <h3 class="box-title pull-right"><a href="{{route('country.create')}}" class="btn btn-success pull-right">{{ trans('countries.add_new') }}</a></h3>
            @endcan -->
          </div>
          <div class="box-body">
            <table id="country" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('countries.country_name')}}</th>
                  <th>{{trans('countries.country_code')}}</th>
                  <th>{{trans('countries.flag')}}</th>
                  <!-- <th>{{trans('common.status')}}</th> -->
                  <th>{{trans('common.action')}}</th>
                </tr>
              </thead>
              
              <tfoot>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('countries.country_name')}}</th>
                  <th>{{trans('countries.country_code')}}</th>
                  <th>{{trans('countries.flag')}}</th>
                  <!-- <th>{{trans('common.status')}}</th> -->
                  <th>{{trans('common.action')}}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
@section('js')
<script type="text/javascript">
    $(document).on('change','.country_status',function(){
        var status = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('country_status')}}",
            data: {
                    "status": status, 
                    "id" : id,  
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                element.next('.loading').css('visibility', 'visible');
            },
            success: function (data) {
              setTimeout(function() {
                    element.next('.loading').css('visibility', 'hidden');
                }, delay);
              toastr.success(data.success);
            },
            error: function () {
              toastr.error(data.error);
            }
        })
    })
  </script>
<script type="text/javascript">
 $(document).ready(function(){
  var lang_url = '';
    if ('{{config("app.locale")}}' == 'ar'){
      lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
  }
   $('#country').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span>',
              url: lang_url
      },
      ajax: {
          url: "{{route('dt_country')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id'},
         { data: 'name',orderable:false},
         { data: 'country_code',orderable:false },
         { data: 'flag',orderable:false },
         /*{ data: 'status',
            mRender : function(data, type, row) {
                  var status=data;
                  if(status=='1'){
                    type="selected";
                    data='';
                  }else{
                    data='selected';
                    type='';
                  }
                 return '<select class="country_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>{{trans("common.active")}}</option><option value="inactive"'+data+'>{{trans("common.inactive")}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span></span>';
              } 
          },*/
          { 
            mRender : function(data, type, row) {
                  return '<a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a>';
              },orderable:false,
          },
        ]
   });
});
</script>
<script>
  function delete_alert() {
      if(confirm("{{trans('common.confirm_delete')}}")){
        return true;
      }else{
        return false;
      }
    }
</script>

@endsection