@extends('layouts.admin.app')
@section('css')
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
<style type="text/css">
  td form{
    display: inline;
  }
  .dt-buttons{margin-right: 10px}
  .select2-selection {padding: 0px 5px !important}
</style>
@endsection    
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      {{trans('notifications.plural')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
      <li><a href="{{route('notifications.index')}}">{{trans('notifications.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">{{ trans('notifications.title') }}</h3>
          </div>
          <div class="box-body">
            <table id="notification" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('notifications.title_field')}}</th>
                  <th>{{trans('notifications.content')}}</th>
                  <th>{{trans('common.action')}}</th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('notifications.title_field')}}</th>
                  <th>{{trans('notifications.content')}}</th>
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

<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/jszip.min.js')}}"></script>
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/buttons.html5.min.js')}}"></script>
<!-- <script src="{{asset('admin/bower_components/datatables.net-bs/export/buttons.print.min.js')}}"></script> -->
<script type="text/javascript">

</script>
<script type="text/javascript">
  $(document).ready(function(){
    fill_datatable();
    function fill_datatable() {
    var lang_url = '';
        if ('{{config("app.locale")}}' == 'ar'){
          lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
    }
      $('#notification').DataTable({
        dom: 'Blfrtip',
        aaSorting: [[ 0, "desc" ]],  
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        // columnDefs: [ {
        // targets: 6,
        //   visible: false
        // }],
        buttons: [
          // {
          //   extend: 'excel',
          //   text: '<span class="fa fa-file-excel-o"></span> {{trans("common.export")}}',
          //   exportOptions: {
          //       modifier: {
          //           search: 'applied',
          //           order: 'applied'
          //       },
          //       columns: [0,1,2,3,6]
          //   },
          // },
          // {
          //     extend: 'print',
          //     text: '<i class="fa fa-print" aria-hidden="true"></i> {{trans("common.print")}}',
          //     autoPrint: true,
          //     exportOptions: {
          //         modifier: {
          //             search: 'applied',
          //             order: 'applied'
          //         },
          //         columns: [0, 1, 2, 3]
          //     },
          // }

        ],
        processing: true,
        serverSide: true,
        serverMethod:'POST',
        processing: true,
        language: {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span>',
              url: lang_url
        },
        ajax: {
            url: "{{route('dt_notification')}}",
            data: {"_token": "{{csrf_token()}}"},
        },
        columns: [
           { data: 'id'},
           { data: 'title',orderable: false},
           { data: 'content',orderable:false},
           // { data: 'country_name',orderable:false},
           // { data: 'status',
           //    mRender : function(data, type, row) {
           //          var status=data;
           //          if(status=='active'){
           //            type="selected";
           //            data='';
           //          }else{
           //            data='selected';
           //            type='';
           //          }
           //         return '<select class="state_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>'+'{{trans("common.active")}}'+'</option><option value="inactive"'+data+'>'+'{{trans("common.inactive")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">'+'{{trans("common.loading")}}'+'</span></span>';
           //      } 
           //  },
           { 
              mRender : function(data, type, row) {
                    var returns =  '<form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>';
                  @if($user->user_type == 'admin')
                    if(row["redirect_link"]){
                      var route = row["redirect_link"];
                     returns += '<a href="'+route+'"><i class="btn fa fa-eye"></i></a>';
                    }
                  @endif
                  return returns;
                }, orderable: false, searchable: false
            },
              // { data: 'status',visible: false},
          ]
      });
    }

  
    $('.select2').select2();
    
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