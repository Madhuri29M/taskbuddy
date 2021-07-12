@extends('layouts.admin.app')
@section('css')
<style>
  .content-label::after {
    content: "*";
    color: red;
}
</style>
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
<link rel="stylesheet" href="{{asset('adminAsset/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')}}">
<style type="text/css">
  td form{
    display: inline; 
  }
 .no-sort::after { display: none!important; }
</style>
@endsection    
@section('content')
<div class="content-wrapper"> 
  <section class="content-header">
    <h1>
      {{trans('customer_messages.heading')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{trans('common.home')}}</a></li>
      <li><a href="{{route('c_message.index')}}">{{trans('customer_messages.plural')}}</a></li>
    </ol>
  </section>

  <section class="content">
    <div class="row">
      <div class="col-xs-12">
        <div class="box">

          <div class="box-header">
            <form id="delete_all" action="{{route('user_message')}}" method="POST">
               {{csrf_field()}}
               
                  <div id="fields"></div>
                    <div class="modal-body">
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group">
                          <label for="title" class="content-label"> {{trans('customer_messages.notif_title')}}</label>
                          <input type='title' name="title" id='title' class="form-control" placeholder="{{trans('customer_messages.notif_title')}}" required>
                          </div>
                          @error('title')
                            <div class="alert alert-danger">{{ $message }}</div>
                          @enderror
                        </div>
                        <div class="col-md-2">
                          <div class="form-group">
                          <label for="type" class="content-label"> {{trans('customer_messages.type')}}</label>
                         <select class="form-control" name="send_type" id="send_type">
                          <option value="push">{{trans('customer_messages.push')}}</option>
                          <!-- <option value="email">{{trans('customer_messages.email')}}</option> -->
                         </select>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-8">
                          <div class="form-group">
                          <label for="message" class="content-label"> {{trans('customer_messages.message')}}</label>
                           <textarea required id='body' name="body" class="form-control" placeholder="{{trans('customer_messages.message')}}" rows='4'></textarea>
                          @error('body')
                            <div class="alert alert-danger">{{ $message }}</div>
                          @enderror
                          </div>
                        </div>
                      </div>
                    </div>
                  <input type='hidden' readonly id='csrf_token' value="{{csrf_token()}}">
                  <div class="modal-footer">
                    <button type="submit" class="btn btn-success pull-right">{{trans('customer_messages.send')}}</button>
                  </div>
            </form>
          </div>
          <hr>
          <div class="box-body">
            <table id="user" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th class="no-sort">#</th>
                  <th>{{trans('customer_messages.profile_image')}}</th>
                  <th>{{trans('customer_messages.name')}}</th>
                  <th>{{trans('customer_messages.mobile_number')}}</th>
                  <th>{{trans('customer_messages.email')}}</th>
                  <th>{{trans('customer_messages.created_at')}}</th>
                  
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
                  </tr>
              </tbody>
              <tfoot>
                <tr>
                 <th class="no-sort">#</th>
                  <th>{{trans('customer_messages.profile_image')}}</th>
                  <th>{{trans('customer_messages.name')}}</th>
                  <th>{{trans('customer_messages.mobile_number')}}</th>
                  <th>{{trans('customer_messages.email')}}</th>
                  <th>{{trans('customer_messages.created_at')}}</th>
                  
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
 $(document).ready(function(){
  var lang_url = '';
        if ('{{config("app.locale")}}' == 'ar'){
          lang_url ="{{asset('adminAsset/bower_components/datatables.net-bs/Arabic.json')}}";
  }
  var table = $('#user').DataTable({
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      processing: true,
      serverSide: true,
      serverMethod:'POST',
      processing: true,
      language: {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">{{trans("common.loading")}}</span>',
              url: lang_url
      },
      ajax: {
          url: "{{route('dt_user_index')}}",
          data: {"_token": "{{csrf_token()}}"},
      },
       columnDefs: [
         {
            targets: 0,
            checkboxes: {
               selectRow: true
            }
         }
      ],
      select: {
         style: 'multi'
      },
      columns: [
         { data: 'id',
            mRender : function(data, type, row) { 
                return row['number'];
              },
          },
         { 
            mRender : function(data, type, row) {  
                 return '<img src="'+ row["profile_picture"]+'" height="100px">'; 
              },orderable: false, searchable: false 
          },
         { data: 'first_name' ,orderable:false},
         { data: 'mobile_number',orderable:false},
         { data: 'email',orderable:false},
         { data: 'created_at',orderable:false},
         
        ]
   });
    // Handle form submission event
   $('#delete_all').on('submit', function(e){
      var rows_selected = table.column(0).checkboxes.selected();
    
      // Iterate over all selected checkboxes
      $.each(rows_selected, function(index, rowId){
         // Create a hidden element
         $('#fields').append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
         );
      });
   });
});
</script>

@endsection