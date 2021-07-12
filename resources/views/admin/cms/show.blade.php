@extends('layouts.admin.app')
@section('content')
  <section class="content-header">
    <h1>
       {{ trans('cms.cms') }}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home') }}</a></li>
      <li><a href="{{route('cms.index')}}">{{trans('cms.show')}}</a></li>
      <li class="active">{{ trans('cms.cms') }}</li>
    </ol>
  </section>
  <section class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
           <div class="box-header with-border">
                <h3 class="box-title">{{ trans('title.cms_detail') }}</h3>
                @can('cms-list')
                <ul class="pull-right">
                    <a href="{{route('cms.index')}}" class="btn btn-success">
                        <i class="fa fa-arrow-left"></i>
                        {{ trans('common.back') }}
                    </a>
                </ul>
                @endcan
            </div>
          <div class="box-body">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    @foreach(config('app.locales') as $lk=>$lv)
                    <li role="presentation" class="@if($lk=='en') active @endif">
                      <a href="#abc_{{$lk}}" aria-controls="" role="tab" data-toggle="tab" aria-expanded="true">
                                {{$lv['name']}}
                      </a>
                    </li>  
                    @endforeach
                </ul>
              </div>
                <div class="tab-content" style="margin-top: 10px;">
                  @foreach(config('app.locales') as $lk=>$lv)
                    <div role="tabpanel" class="tab-pane @if($lk=='en') active @endif" id="abc_{{$lk}}">
                      <div class="form-group">
                        <label for="content:{{$lk}}" class="content-label">{{trans('cms.content')}}</label>
                        <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.content')}}" name="content:{{$lk}}" type="text" value="{{$cms->translate($lk)->content}}" disabled>
                        @if ($errors->has('content:'.$lk)) <p style="color:red;">{{ $errors->first('content:'.$lk) }}</p> @endif
                        <strong class="help-block"></strong>
                      </div>

                      <div class="form-group">
                        <label for="page_name:{{$lk}}" class="content-label">{{trans('cms.page_name')}}</label>
                        <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.page_name')}}" name="page_name:{{$lk}}" type="text" value="{{$cms->translate($lk)->page_name}}"disabled>
                        <strong class="help-block"></strong>
                      </div>

                    </div>
                  @endforeach

                    <div class="form-group">
                        <label for="slug" class="content-label">{{trans('cms.slug')}}</label>
                        <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.slug')}}" name="slug" type="text" value="{{$cms->slug}}"disabled>
                          @if ($errors->has('slug')) <p style="color:red;">{{ $errors->first('slug') }}</p> @endif
                      <strong class="help-block"></strong>
                    </div>
                      
                    <div class="form-group">
                      <label for="display_order:{{$lk}}" class="content-label">{{trans('cms.diplay_order')}}</label>
                      <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('cms.diplay_order')}}" name="display_order" type="number" value="{{$cms->display_order}}" disabled>
                       @if ($errors->has('display_order')) <p style="color:red;">{{ $errors->first('display_order') }}</p> @endif
                      <strong class="help-block"></strong>
                    </div>
                 
                </div>
                
              </div>
                  <div class="modal-footer">
                <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{trans('common.submit')}}</button>
              </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@section('js')
  <script src="{{ asset('adminAsset/custom/faqs.js') }}"></script>
@endsection


