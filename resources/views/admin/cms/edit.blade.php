@extends('layouts.admin.app')
@section('css')
<style>
  .content-label::after {
    content: "*";
    color: red;
}
</style>
@endsection 
@section('content')
<!-- 
<style type="text/css">
  .box-header {
    margin-right: 8px;
    margin-left: 6px;
    margin-top: 7px; 
  } 
</style> -->
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      {{trans('cms.edit_cms')}} : {{$cms->page_name}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('cms.index')}}">{{ trans('cms.cms_index') }}</a></li>
      <li class="active">{{trans('cms.edit_cms')}}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
           <div class="box-header with-border">
               <!--  <h3 class="box-title">{{ trans('cms.details') }}</h3> -->
               
                <!-- <ul class="pull-right">
                    <a href="{{route('cms.index')}}" class="btn btn-success">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul> -->
               
            </div>
          <div class="box-body">
            <form method="POST" id="cmsForm" action="{{route('cms.update', $cms->id)}}" accept-charset="UTF-8">
              <input name="_method" type="hidden" value="PUT">
              @csrf
                <div class="tab-content" style="margin-top: 10px;">
                  <div class="form-group">
                    <label for="content" class="content-label">{{trans('cms.content')}}</label>
                    <textarea class="form-control" id="summary-ckeditor"placeholder="{{trans('cms.content')}}" name="content" type="text" value="">
                      {{$cms->content}}
                    </textarea>
                    @error('content')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <label for="page_name" class="content-label">{{trans('cms.page_name')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.page_name')}}" name="page_name" type="text" value="{{$cms->page_name}}">
                    @error('page_name')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
             
               <!--  <div class="form-group">
                  <label for="slug" class="content-label">{{trans('cms.slug')}}</label>
                  <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.slug')}}" name="slug" type="text" value="{{$cms->cms_data->slug}}">
                  @error('slug')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div> -->
                  
               <!--  <div class="form-group">
                  <label for="display_order" class="content-label">{{trans('cms.display_order')}}</label>
                  <input class="form-control" maxlength="255" placeholder="{{trans('cms.display_order')}}" name="display_order" type="number" value="{{$cms->cms_data->display_order}}">
                  @error('display_order')
                    <div class="alert alert-danger">{{ $message }}</div>
                  @enderror
                </div> -->
                 
              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{trans('common.submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

@endsection

@section('js')
 <script src="{{ asset('admin/bower_components/ckeditor/ckeditor.js') }}"></script>
<script>
    if ($(".tab-content").find('.alert-danger').text()) {
    $.each($(".tab-content").find('.alert-danger'), function (index, value) {
        if ($(this).text()) {
            $(".tab-content").children('.tab-pane').removeClass('active');
            $(this).closest('.tab-pane').addClass('active');
            $(".nav-tabs").children('li').removeClass('active');
            var id = $(this).closest('.tab-pane').attr('id');
            $.each($(".nav-tabs").children('li'), function (i, a) {
                if (('#' + id) == $(this).children('a').attr('href'))
                    $(this).addClass('active');
            });
            return false;
        }
    });
}
</script>
<script>
    CKEDITOR.replaceAll();
    CKEDITOR.config.allowedContent = true;
</script>

@endsection


