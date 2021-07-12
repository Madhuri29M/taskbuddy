@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
     {{ trans('cms.cms_index') }} 
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> {{ trans('common.home') }}</a></li>
      <li><a href="{{route('cms.index')}}"> {{ trans('cms.cms_index') }} </a></li>
    </ol>
  </section>
  <section class="content" >
    <div class="row">
      <div class="col-xs-12">    
        <!-- <h3 class="box-title pull-right"><a href="{{route('cms.create')}}" class="btn btn-success pull-right">{{ trans('cms.add_cms') }}</a></h3>   -->
      </div>
    </div>
    <div class="row">
        @foreach($cms as $cm)
        <?php if($cm->cms_data->slug == 'privacy_policy'){
          $page_name = $cm->page_name;
        }else if($cm->cms_data->slug == 'terms_conditions'){
          $page_name = $cm->page_name;
        }else if($cm->cms_data->slug == 'about_us'){
          $page_name = $cm->page_name;
        }
        else{
          $page_name = '';
        }
        ?>
          <div class="col-md-6 col-sm-8 col-xs-12" >
            <div class="info-box">
             <span class="info-box-icon bg-aqua"><i class="fa fa-file-text"></i></span>
              <div class="info-box-content">
                 <span class="info-box-text"><b>{{$page_name}}</b></span>
                <!--  <span class="info-box-text"><b>Page Name: </b>{{$cm->page_name}} </span> -->
                 <span class="info-box-text"><b> {{trans('cms.language:')}} </b>{{$cm->locale}} </span>         
                 <span class="pull-right"><b> <a href ="{{route('cms.edit',$cm->id)}}"><i class="fa fa-edit"></i>  {{trans('cms.edit_detail')}}</a></b></span> 
              </div>
            </div>
          </div>
        @endforeach
    </div>    
  </section>
</div>
@endsection
