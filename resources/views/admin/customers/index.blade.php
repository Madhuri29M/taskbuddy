@extends('layouts.admin.app')
@section('css')
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
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
      {{ trans('customers.heading') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('customers.index')}}">{{ trans('customers.plural') }}</a></li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">{{ trans('customers.title') }}
          </div>
          <div class="box-body">
            <table id="customers" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>{{ trans('common.id') }}</th>
                  <th>{{ trans('customers.name') }}</th>
                  <!-- <th>{{ trans('customers.last_name') }}</th> -->
                  <th>{{ trans('customers.email') }}</th>
                  <th>{{ trans('customers.phone_number') }}</th>
                  <!-- <th>{{ trans('customers.gender') }}</th> -->
                  <th>{{ trans('customers.registered_on') }}</th>
                  <th>{{ trans('customers.verification_status') }}</th>
                  <th>{{ trans('common.status') }}</th>
                  <th>{{ trans('common.action') }}</th>
                  <th hidden="hidden">{{trans('common.status')}} </th>
                </tr>
              </thead>
              <tbody>
               
              </tbody>
              <tfoot>
                 <tr>
                  <th>{{ trans('common.id') }}</th>
                  <th>{{ trans('customers.name') }}</th>
                  <!-- <th>{{ trans('customers.last_name') }}</th> -->
                  <th>{{ trans('customers.email') }}</th>
                  <th>{{ trans('customers.phone_number') }}</th>
                  <!-- <th>{{ trans('customers.gender') }}</th> -->
                  <th>{{ trans('customers.registered_on') }}</th>
                  <th>{{ trans('customers.verification_status') }}</th>
                  <th>{{ trans('common.status') }}</th>
                  <th>{{ trans('common.action') }}</th>
                  <th hidden="hidden">{{trans('common.status')}} </th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-labelledby="NotifyLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">{{ trans('customers.send_notification') }} </h4>
        </div>
        <div class="modal-body">
          <form class="form-group" method="PUT" id="notifyForm">
            <div class="row">
              <input class="form-control" id="notify_id" required="true" name="notify_id" type="hidden">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="name" class="content-lable-astrisk">{{trans('customers.title_text') }}</label>
                    <input class="form-control" placeholder="{{trans('panel.message_subject')}}" required="true" name="title" type="text" id="title">
                  </div>
                </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="message" class="content-lable-astrisk">{{trans('customers.message') }}</label>
                  <textarea class="form-control" placeholder="{{trans('panel.message_title')}}" required="true" name="body" id="content" rows="3"> </textarea>
                </div>
              </div>

            </div>
          </form>
         
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" type="button" id="send_notification"> {{trans('customers.send') }}</button>
          <button type="button" class="btn btn-default" data-dismiss="modal"> {{trans('customers.close') }}</button>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection 

@section('js')
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/jszip.min.js')}}"></script>
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/buttons.html5.min.js')}}"></script>
<script src="{{asset('adminAsset/bower_components/datatables.net-bs/export/buttons.print.min.js')}}"></script>
<script type="text/javascript">
  $(document).on('change','.status',function(){
    var status = $(this).val();
    var id = $(this).attr('id');
    var delay = 500;
    var element = $(this);
    $.ajax({
        type:'post',
        url: "{{route('status')}}",
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
 $(document).ready(function(){
    fill_datatable();
    function fill_datatable() {
    var lang_url = '';
    if ('{{config("app.locale")}}' == 'ar'){
      lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
    }
  $('#customers').DataTable({
    aaSorting: [[ 0, "desc" ]],
    dom: 'Blfrtip',
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100,"All"]],
        pageLength: 10,
        buttons: [
          {
            extend: 'excel',
            text: '<span class="fa fa-file-excel-o"></span> {{trans("common.export")}}',
            exportOptions: {
                modifier: {
                    search: 'applied',
                    order: 'applied'
                },
                columns: [0, 1, 2 , 3 ,4 ,7]
            },
          },
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
          url: "{{route('ajax_customers')}}",
          data: {"_token": "{{csrf_token()}}"},
        },
        columns: [
          { 
            data: 'id',
            mRender : function(data, type, row) { 
                // return "<a href == row['show']";
                return '<a class="" href="'+row["show"]+'">'+row.id+'</a>';
              },
          },
          { 
            data: 'name',
            mRender : function(data, type, row) { 
              return '<a class="" href="'+row["show"]+'">'+row.name+'</a>';
            },orderable:false,
          },
          { 
            data: 'email',
            mRender : function(data, type, row) { 
              return '<a class="" href="'+row["show"]+'">'+row.email+'</a>';
            },orderable:false,
          },
          { 
            data: 'mobile_number',
            mRender : function(data, type, row) { 
              return '<a class="" href="'+row["show"]+'">'+row.mobile_number+'</a>';
            },orderable:false,
          },
          { 
            data: 'registered_on',
            mRender : function(data, type, row) { 
              return '<a class="" href="'+row["show"]+'">'+row.registered_on+'</a>';
            },orderable:false,
          },
          { 
            data: 'verified',
            mRender : function(data, type, row) { 
              if(row.verified == '1'){
                var ver = "{{trans('customers.verified')}}";
              } else {
                var ver = "{{trans('customers.not_verified')}}";
              }
              return '<a class="" href="'+row["show"]+'">'+ver+'</a>';
            },
            orderable:false,
          },
          { 
            data: 'status',
            mRender : function(data, type, row) {
                  var status=data;
                  // console.log(status);
                  active_selected = '';
                  inactive_selected = '';
                  blocked_selected = '';

                  if (status=='active'){
                    active_selected="selected";
                  }
                  else if(status=='inactive'){
                  	inactive_selected="selected";
                  }
                  else {
                    blocked_selected='selected';
                  }
                 return '<select class="status form-control" id="'+row["id"]+'"><option value="active"'+active_selected+'>{{trans("common.active")}}</option><option value="inactive"'+inactive_selected+'>{{trans("common.inactive")}}</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span></span>';
            } 
          },
          { 
            mRender : function(data, type, row) {
                return '<form action="'+row["show"]+'" method="get"><button class="btn" type="submit"><i class="fa fa-eye"></i></button></form> <form action=""'+row["id"]+'" <button class="btn notifyModal" type="submit" data-id="'  +row["id"]+'"><i class="fa fa-send"></i></button></form>'; 
            }, orderable: false, searchable: false
          },
          { 
            data: 'status',
            visible: false
          },
        ]
      });
    };
  });
  function delete_alert() {
    if(confirm("{{trans('common.confirm_delete')}}")){
      return true;
    }else{
      return false;
    }
  }
  $(document).on('click','.notifyModal', function(){
    var id = $(this).data('id');
    $('#notify_id').val(id);
    $('#title').val('');
    $('#content').val('');
    $("label.error").remove();
    $('#notify').modal('show');
  })
  $('#send_notification').click(function(){
    var validator = $('#notifyForm').validate({ 
        rules: {
            notify_id: {
                required: true
            },
            title: {
                required: true
            },
            body: {
                required: true
            }
        },
        messages:{
          title:{
            required:"{{trans('customer_messages.enter_title')}}"
          },
          body:{
            required:"{{trans('customer_messages.message_not_empty')}}",
          }
        }
      });
      
      validator.form();
     
      if (validator.valid()) 
        {
      
        }
      else 
        {
            return false;
        }
  
      var title        = $('#title').val();
      var body         = $('#content').val();
      var notify_id    = $('#notify_id').val();
     
        $.ajax({
            type:'post',
            url: "{{ route('send_notification') }}",
            data: {
                    "id"         : notify_id, 
                    "title"      : title,
                    "body"       : body,  
                    "_token": "{{ csrf_token() }}"
                  },
            beforeSend: function () {
                $('#send_notification').html('Sending..<span id="loader" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">a</span></span>');
                $('#loader').css('visibility', 'visible');
            },
            success: function (data) {
              $('#send_notification').html('Send');
              $('#loader').css('visibility', 'hidden');
              if(data.type == 'error'){
                toastr.error(data.message);
              }else{
                $('#notify').modal('hide');
                toastr.success(data.message);
              }
            },
            error: function () {
              $('#send_notification').html('Send');
              $('#loader').css('visibility', 'hidden');
              toastr.error('Work in progress');
            }
        })
    })
  
</script>
@endsection