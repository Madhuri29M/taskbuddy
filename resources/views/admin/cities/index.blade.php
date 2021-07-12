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
      {{trans('cities.plural')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{trans('common.home')}}</a></li>
      <li><a href="{{route('cities.index')}}">{{trans('cities.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <h3 class="box-title">{{ trans('cities.title') }}</h3>
            <h3 class="box-title pull-right"><a href="{{route('cities.create')}}" class="btn btn-success pull-right">{{trans('cities.add_new')}}</a></h3>
          </div>
          <div class="box-body">
            <table id="city" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('cities.name')}}</th>
                  <th>{{trans('cities.state_name')}}</th>
                  <th>{{trans('cities.country_name')}}</th>
                  <th>{{trans('common.status')}}</th>
                  <th>{{trans('common.action')}}</th>
                  <th hidden="hidden">{{trans('common.status')}} </th>
                </tr>
              </thead>
              <tfoot>
                <tr>
                  <th>{{trans('common.id')}}</th>
                  <th>{{trans('cities.name')}}</th>
                  <th>{{trans('cities.state_name')}}</th>
                  <th>{{trans('cities.country_name')}}</th>
                  <th>{{trans('common.status')}}</th>
                  <th>{{trans('common.action')}}</th>
                  <th hidden="hidden">{{trans('common.status')}} </th>
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
  $(document).on('change','.state_status',function(){
      var status = $(this).val();
      var id = $(this).attr('id');
      var delay = 500;
      var element = $(this);
      $.ajax({
          type:'post',
          url: "{{route('status_cities')}}",
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
    fill_datatable();
    function fill_datatable() {
      var lang_url = '';
        if ('{{config("app.locale")}}' == 'ar'){
          lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
      }
      $('#city').DataTable({
        dom: 'Blfrtip',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        pageLength: 10,
        // columnDefs: [ {
        // targets: 6,
        //   visible: false
        // }],
        buttons: [
          {
            extend: 'excel',
            text: '<span class="fa fa-file-excel-o"></span> {{trans("common.export")}}',
            exportOptions: {
                modifier: {
                    search: 'applied',
                    order: 'applied'
                },
                columns: [0,1,2,3,6]
            },
          },
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
            url: "{{route('dt_cities')}}",
            data: {"_token": "{{csrf_token()}}"},
        },
        columns: [
           { data: 'id'},
           { data: 'name',orderable: false},
           { data: 'state_name',orderable:false},
           { data: 'country_name',orderable:false},
           { data: 'status',
              mRender : function(data, type, row) {
                    var status=data;
                    if(status=='active'){
                      type="selected";
                      data='';
                    }else{
                      data='selected';
                      type='';
                    }
                   return '<select class="state_status form-control" id="'+row["id"]+'"><option value="active"'+type+'>'+'{{trans("common.active")}}'+'</option><option value="inactive"'+data+'>'+'{{trans("common.inactive")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">'+'{{trans("common.loading")}}'+'</span></span>';
                } 
            },
           { 
              mRender : function(data, type, row) {
                   return '<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a><a class="btn" href="'+row["show"]+'"><i class="fa fa-eye"></i></a><form action="'+row["delete"]+'" method="post"><button class="btn" type="submit" onclick=" return delete_alert()"><i class="fa fa-trash"></i></button>@method("delete")@csrf</form>';  
                }, orderable: false, searchable: false
            },
              { data: 'status',visible: false},
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