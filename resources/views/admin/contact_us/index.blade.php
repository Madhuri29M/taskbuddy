@extends('layouts.admin.app')
@section('css')
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
<style>
  .content-label::after {
    content: "*";
    color: red;
}
</style>
@endsection    
@section('content')
<div class="content-wrapper"> 
  <section class="content-header">
    <h1>
      {{ trans('contact_us.heading') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('contact_us.index')}}">{{ trans('contact_us.plural') }}</a></li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box box-info">
          <div class="box-header">
            <div class="row">
              <div class="col-xs-3">
                <label for="from_date">{{trans('payout.from_date')}}</label>
                <input class="form-control datetimepicker" placeholder="{{trans('payout.from_date')}}"  type="text" id="from_date"autocomplete="off" value="{{date('Y-m-d', strtotime('-30 days'))}}">
              </div>
              <div class="col-xs-3">
                <label for="to_date" >{{trans('payout.to_date')}}</label>
                <input class="form-control datetimepicker" placeholder="{{trans('payout.to_date')}}"  type="text" id="to_date"autocomplete="off" value="{{date('Y-m-d')}}">
              </div>
             <div class="col-xs-3">
                <label for="status" >{{trans('common.status')}}</label>
                <select name="status" id="status" class="form-control">
                  <option value="" selected>{{trans('payout.all')}}</option>
                  <option value="pending">{{trans("contact_us.pending")}}</option>
                  <option value="acknowledged">{{trans("contact_us.acknowledged")}}</option>
                  <option value="resolved">{{trans("contact_us.resolved")}}</option>
                </select>
              </div>
              <div class="col-xs-3">
                <label for="status" >{{ trans('contact_us.user_type') }}</label>
                <select name="user_type" id="user_type" class="form-control">
                  <option value="" selected>{{trans('payout.all')}}</option>
                  <option value="vendor">{{trans("vendors.partner")}}</option>
                  <option value="individual">{{trans("vendors.individual")}}</option>
                  <option value="employee">{{trans("vendors.employee")}}</option>
                  <option value="customer">{{trans("payout.customer")}}</option>
                </select>
              </div>
              
            </div>
          </div>
          <div class="box-body">
            <table id="contactus" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{ trans('contact_us.name') }}</th>
                  <th>{{ trans('contact_us.user_type') }}</th>
                  <th>{{ trans('contact_us.message') }}</th>
                  <th>{{ trans('contact_us.comment') }}</th>
                  <th>{{ trans('common.status') }}</th>
                  <th>{{ trans('contact_us.date') }}</th>
                </tr>
              </thead>
              <tbody>
                 
              </tbody>
              <tfoot>
                <tr>
                  <th>#</th>
                  <th>{{ trans('contact_us.name') }}</th>
                  <th>{{ trans('contact_us.user_type') }}</th>
                  <th>{{ trans('contact_us.message') }}</th>
                  <th>{{ trans('contact_us.comment') }}</th>
                  <th>{{ trans('common.status') }}</th>
                  <th>{{ trans('contact_us.date') }}</th>
                 
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="modal fade" id="contact_us" tabindex="-1" role="dialog" aria-labelledby="NotifyLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">{{trans('contact_us.send_comment')}}</h4>
        </div>
        <div class="modal-body">
          <form class="form-group" method="PUT" id="contact_usForm">
            <div class="row">
              <input class="form-control" id="contact_us_id" required="true" name="contact_us_id" type="hidden">          
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="name" class="content-label" >{{trans('contact_us.comment')}}</label>
                  <textarea class="form-control" placeholder="{{trans('contact_us.comment')}}" required="true" name="comment" id="comment" rows="3"> </textarea>
                </div>
              </div>

            </div>
          </form>
         
        </div>
        <div class="modal-footer">
          <button class="btn btn-default" type="button" id="send_comment">{{trans('contact_us.send')}}</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('contact_us.close')}}</button>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<script src="https://cdn.datatables.net/fixedcolumns/3.3.2/js/dataTables.fixedColumns.min.js"></script>
<script type="text/javascript">
    $(document).on('change','.contact_status',function(){
      if ($(this).val() == "resolved"){
        var status = $(this).val();
        var id = $(this).attr('id');
        $('#contact_us').modal('show');
        $('#send_comment').click(function(){

        var validator = $('#contact_usForm').validate({ 
          rules: {
                  id: {
                      required: true
                  },
                  comment: {
                      required: true
                  }
              },
              messages:{
                comment:{
                  required:"{{trans('contact_us.message_cannot_be_empty')}}",
                }
              }
          });

          if (validator.valid()) 
            {
          
            }
          else 
            {
                return false;
            }
          var comment      =  $('#comment').val();
          var delay = 500;
          $.ajax({
                type:'post',
                url: "{{ route('r_status') }}",
                data: {
                        "id"         : id, 
                        "status": status, 
                        "comment"       : comment,  
                        "_token": "{{ csrf_token() }}"
                      },
                beforeSend: function () {
                    $('#send_comment').html('Sending..<span id="loader" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">a</span></span>');
                    $('#loader').css('visibility', 'visible');
                },
                success: function (data) {
                  $('#send_comment').html('Send');
                  $('#comment').val("");
                  $('#loader').css('visibility', 'hidden');
                  if(data.type == 'error'){
                    toastr.error(data.message);
                  }else{
                    $('#contact_us').modal('hide');
                    location.reload();
                    toastr.success(data.message);
                  }
                },
                error: function () {
                  $('#send_comment').html('Send');
                  $('#loader').css('visibility', 'hidden');
                  toastr.error('Work in progress');
                }
            })
        })
      }
      else{
        var status = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('contact_status')}}",
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
              if(data.type == 'error'){
                toastr.error(data.error);
              }else{
                toastr.success(data.success);
              }
            },
            error: function () {
              toastr.error(data.error);
            }
        })
      }
    })
</script>
<script type="text/javascript">
 $(document).ready(function(){
  // alert();
   var from_date = $('#from_date').val();
   var to_date = $('#to_date').val();
   var status = $('status').val();
   var user_type = $('user_type').val();

  fill_datatable(from_date,to_date,status,user_type);
  function fill_datatable(from_date,to_date,status,user_type) {
    var lang_url = '';
      if ('{{config("app.locale")}}' == 'ar'){
          lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
      }
      var table = $('#contactus').removeAttr('width').DataTable({
        aaSorting: [[ 0, "desc" ]],
        processing: true,
        serverSide: true,
        serverMethod:'POST',
        processing: true,
        language: {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span>',
              url: lang_url
        },
        ajax: {
            url: "{{route('ajax_contact_us')}}",
            data: {
                    "_token": "{{csrf_token()}}", 
                    'from_date': from_date,
                    'to_date': to_date,
                    'status': status,
                    'user_type': user_type,
                  },
        },
        columnDefs: [
            {
                render: function (data, type, full, meta) {
                  if(data != null)
                  {
                    return "<div style='white-space:normal;overflow-wrap: break-word;width:40px;'>" + data + "</div>";
                  }
                  else
                  {
                    return "<div style='white-space:normal;overflow-wrap: break-word;width:40px;'></div>";
                  }
                },
                targets: 4
            }
        ],
        columns: [
            { data: 'id',
              mRender : function(data, type, row) { 
                  return row['id'];
                } 
            },
           { 
              mRender : function(data, type, row) {
                    return row['name'];  
                }, orderable: false, searchable: false
            },
           { 
              mRender : function(data, type, row) {
                    return row['user_type'];  
                }, orderable: false, searchable: false
            },

           { data: 'description', orderable: false, searchable: false},
           { data: 'comment', orderable: false, searchable: false},
           {
              mRender : function(data, type, row) {

                    var status = row['status'];
                    var new_ticket = '';
                    var resolved = '';
                    var acknowledged = '';
                    var rejected = '';

                    if(status == 'acknowledged'){
                      acknowledged = "selected";

                    }else if(status == 'resolved'){
                      resolved = 'selected';

                    }else{
                      new_ticket = 'selected';
                    }
                    if(resolved == "selected"){
                      return '{{trans("contact_us.resolved")}}';  
                    }else{
                      return '<select class="contact_status form-control" id="'+row["id"]+'"><option value="acknowledged"'+acknowledged+'>'+'{{trans("contact_us.acknowledged")}}'+'</option><option value="resolved"'+resolved+'>'+'{{trans("contact_us.resolved")}}'+'</option><option value="new_ticket"'+new_ticket+'>'+'{{trans("contact_us.pending")}}'+'</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">'+'{{trans("common.loading")}}'+'</span></span>';
                    }
                },orderable: false, searchable: false 
           },
           { data: 'created_at',
              mRender : function(data, type, row) { 
                  return row['date'];
                } 
            },
          ]
     });
  }
    //cuisines filter
    $('#from_date').datetimepicker().on('dp.change', function (event) {
      $('#contactus').DataTable().destroy();
      fill_datatable($('#from_date').val(), $('#to_date').val(),$('#status').val(),$('#user_type').val());  
    });

    $('#to_date').datetimepicker().on('dp.change', function (event) {
      $('#contactus').DataTable().destroy();
      fill_datatable($('#from_date').val(), $('#to_date').val(),$('#status').val(),$('#user_type').val());  
    })

     $('#status').on('change', function (event) {

      $('#contactus').DataTable().destroy();
      
      fill_datatable($('#from_date').val(), $('#to_date').val(),$('#status').val(),$('#user_type').val());  
    })
     $('#user_type').on('change', function (event) {

      $('#contactus').DataTable().destroy();
      fill_datatable($('#from_date').val(), $('#to_date').val(),$('#status').val(),$('#user_type').val());  
    })
});
</script>

<script>
    function delete_alert() {
      if(confirm("Are you sure want to delete this item?")){
        return true;
      }else{
        return false;
      }
    }

</script>
<script type="text/javascript">
  $(".datetimepicker").datetimepicker({
    // format: 'YYYY-MM-DD H:mm',
    // minDate: new Date().setHours(0,0,0,0),
    format: 'YYYY-MM-DD',
    icons:{
        time: 'glyphicon glyphicon-time',
        date: 'glyphicon glyphicon-calendar',
        previous: 'glyphicon glyphicon-chevron-left',
        next: 'glyphicon glyphicon-chevron-right',
        today: 'glyphicon glyphicon-screenshot',
        up: 'glyphicon glyphicon-chevron-up',
        down: 'glyphicon glyphicon-chevron-down',
        clear: 'glyphicon glyphicon-trash',
        close: 'glyphicon glyphicon-remove'
    },
    locale : '{{config("app.locale")}}'
  });

  $(".datetimepicker2").datetimepicker({
    // format: 'YYYY-MM-DD H:mm',
    maxDate: new Date().setHours(0,0,0,0),
    format: 'YYYY-MM-DD',
    icons:{
        time: 'glyphicon glyphicon-time',
        date: 'glyphicon glyphicon-calendar',
        previous: 'glyphicon glyphicon-chevron-left',
        next: 'glyphicon glyphicon-chevron-right',
        today: 'glyphicon glyphicon-screenshot',
        up: 'glyphicon glyphicon-chevron-up',
        down: 'glyphicon glyphicon-chevron-down',
        clear: 'glyphicon glyphicon-trash',
        close: 'glyphicon glyphicon-remove'
    },
    locale : '{{config("app.locale")}}'
  });

  $('.multiselect-input').attr('autocomplete','off');
</script>
@endsection