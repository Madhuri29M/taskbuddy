@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
      {{ trans('cms.create_cms') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('cms.home') }}</a></li>
      <li><a href="{{route('cms.index')}}">{{ trans('cms.cms_index') }}</a></li>
      <li class="active"> {{ trans('cms.create_cms') }} </li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
           <!-- <div class="box-header with-border">
                <h3 class="box-title">{{ trans('cms.details') }}</h3>
                <ul class="pull-right">
                    <a href="{{route('cms.index')}}" class="btn btn-success">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
            </div> -->
          <div class="box-body">
            <form method="POST" id="message_templateForm" action="{{route('cms.store')}}" accept-charset="UTF-8">
              @csrf
              <div class="model-body">

                <ul class="nav nav-tabs" role="tablist">
                  @foreach(config('app.locales') as $lk=>$lv)
                    <li role="presentation" class="@if($lk=='en') active @endif">
                      <a href="#abc_{{$lk}}" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                                {{$lv['name']}}
                      </a>
                    </li>  
                  @endforeach
                </ul>
                <div class="tab-content" style="margin-top: 10px;">
                  @foreach(config('app.locales') as $lk=>$lv)
                    <div role="tabpannel" class="tab-pane @if($lk=='en') active @endif" id="abc_{{$lk}}">
                      
                      <div class="form-group">
                        <label for="content:{{$lk}}" class="content-label">{{trans('cms.content')}}</label>
                        <textarea class="form-control" id="summary-ckeditor" class="form-control" rows ='4' placeholder="{{trans('cms.content')}}" name="content:{{$lk}}" type="text" value=""> </textarea>
                        @error('content:'.$lk)
                          <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                      </div>

                      <div class="form-group">
                        <label for="page_name:{{$lk}}" class="content-label">{{trans('cms.page_name')}}</label>
                        <input class="form-control" placeholder="{{trans('cms.page_name')}}" name="page_name:{{$lk}}" type="text" value="">
                        @error('page_name:'.$lk)
                          <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                      </div>  
                    </div>    

                  @endforeach

                  <div class="form-group">
                    <label for="slug" class="content-label">{{trans('cms.slug')}}</label>
                    <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.slug')}}" name="slug" type="text" value="{{ old('slug') }}">
                    @error('slug')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group">
                    <label for="display_order" class="content-label">{{trans('cms.display_order')}}</label>
                    <input class="form-control" placeholder="{{trans('cms.display_order')}}" name="display_order" type="number" value="{{ old('display_order') }}">
                    @error('display_order')
                      <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                  </div> 
                </div> 
              </div>

              <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{trans('Submit')}}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
<script src="{{ asset('adminAsset/bower_components/ckeditor/ckeditor.js') }}"></script>
@section('js')
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
</script>

@endsection