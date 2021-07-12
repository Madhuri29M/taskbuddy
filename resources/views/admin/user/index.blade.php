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
      {{trans('panel.users_list')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('panel.home')}}</a></li>
      <li><a href="{{route('user.index')}}">{{trans('panel.users_list')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">
           <div class="box-header">
            <h3 class="box-title pull-right"><a href="{{route('user.export')}}" class="btn btn-success pull-right " onclick="exportTasks(event.target);">{{trans('panel.export_data')}}</a></h3>
          </div>
          <div class="box-body">
            <table id="user" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>{{trans('panel.name')}}</th>
                  <th>{{trans('panel.mobile_number')}}</th>
                  <th>{{trans('panel.email')}}</th>
                  <th>{{trans('panel.created_at')}}</th>
                  <th>{{trans('panel.action')}}</th>
                  <th>{{trans('panel.status')}}</th>
                </tr>
              </thead>
              <tbody>
                
                  <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
             
              </tbody>
              <tfoot>
                <tr>
                  <th>#</th>
                  <th>{{trans('panel.name')}}</th>
                  <th>{{trans('panel.mobile_number')}}</th>
                  <th>{{trans('panel.email')}}</th>
                  <th>{{trans('panel.created_at')}}</th>
                  <th>{{trans('panel.action')}}</th>
                  <th>{{trans('panel.status')}}</th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-labelledby="NotifyLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Send Notification</h4>
      </div>
      <div class="modal-body">
        <form class="form-group" method="PUT" id="notifyForm">
          <div class="row">
            <input class="form-control" id="notify_id" required="true" name="notify_id" type="hidden">
            
              <div class="col-md-6">
                <div class="form-group">
                  <label for="name">Title</label>
                  <input class="form-control" placeholder="Enter Title" required="true" name="title" type="text" id="title">
                 
                </div>
              </div>
          </div>
           
          <div class="row">

            <div class="col-md-12">
              <div class="form-group">
                <label for="name">Message</label>
                <textarea class="form-control" placeholder="Enter Message" required="true" name="body" id="content" rows="3"> </textarea>
              </div>
            </div>

          </div>
        </form>
       
      </div>
      <div class="modal-footer">
        <button class="btn btn-default" type="button" id="send_notification">Send</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection
@section('js')
<script type="text/javascript">

    $(document).on('change','.user_status',function(){
        var status = $(this).val();
        var id = $(this).attr('id');
        var delay = 500;
        var element = $(this);
        $.ajax({
            type:'post',
            url: "{{route('user_status')}}",
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
    })
</script>


<script type="text/javascript">
 $(document).ready(function(){
  var lang_url = '';
  if ('{{config("app.locale")}}' == 'ar'){
    lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
  }
   $('#user').DataTable({
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span>',
              url: lang_url
      },
      ajax: {
          url: "{{route('dt_user')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
      columns: [
         { data: 'id',
            mRender : function(data, type, row) { 
                return row['number'];
              }, 
          },
         { data: 'name' },
         { data: 'phone_number' },
         { data: 'email' },
         { data: 'created_at' },
         { 
            mRender : function(data, type, row) {
                 // return '<a class="btn" href="'+row["edit"]+'"><i class="fa fa-edit"></i></a>';  
                 return '<form action="'+row["show"]+'" method="get"><button class="btn" type="submit"><i class="fa fa-eye"></i></button></form><button class="btn notifyModal" type="submit" data-id="'+row["id"]+'"><i class="fa fa-send"></i></button>'; 
              },orderable: false, searchable: false 
          },
          {
            mRender : function(data, type, row) {

                  var status = row['status'];

                  if(status == 'inactive' || status == 'pending' || status == 'blocked' ){
                    type = "selected";
                    data = '';
                  }else if(status == 'active'){
                    data = 'selected';
                    type = '';
                  }else{
                    d = 'selected';
                  }

                 return '<select class="user_status" id="'+row["id"]+'"><option value="inactive"'+type+'>Inactive</option><option value="active"'+data+'>Active</option></select><span class="loading" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">Loading...</span></span>';
              },orderable: false, searchable: false 
          },
        ]
   });
});
</script>
<!-- <script type="text/javascript" src="{{ asset('admin/custom/password.js') }}"></script> -->
<script type="text/javascript">

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
            required:'Please enter a title'
          },
          body:{
            required:"Message can't be empty",
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
              // toastr.error('Work in progress');
            }
        })
    })
  
</script>

<script>
   function exportTasks(_this) {
      let _url = $(_this).data('href');
      window.location.href = _url;
   }
</script>
@endsection