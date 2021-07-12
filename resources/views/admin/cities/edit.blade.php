@extends('layouts.admin.app')
@section('content')
<div class="content-wrapper">
  <section class="content-header">
    <h1>
       {{trans('cities.update')}}
    </h1>
    <ol class="breadcrumb">
      <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('common.home')}}</a></li>
      <li><a href="{{route('cities.index')}}">{{ trans('cities.plural')}}</a></li>
      <li class="active">{{trans('cities.update')}}</li>
    </ol>
  </section>
  <section class="content">
    @if ($errors->any())
      <div class="alert alert-danger">
        <b>{{trans('common.whoops')}}</b>
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    <div class="row">
      <div class="col-md-12">
        <div class="box box-warning">
          <div class="box-header with-border">
              <h3 class="box-title">{{ trans('cities.details') }}</h3>
              <ul class="pull-right">
                  <a href="{{route('cities.index')}}" class="btn btn-success">
                      <i class="fa fa-arrow-left"></i>
                      {{ trans('common.back') }}
                  </a>
              </ul>
          </div>
          <div class="box-body">
            <form id="stateForm" method="POST" action="{{route('cities.update',$city->id)}}" accept-charset="UTF-8">
              <input name="_method" type="hidden" value="PUT">
              @csrf
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group ">
                      <label id="image-error" for="countries" class ="content-label">
                      {{trans('cities.countries')}}
                      </label>
                      <select class="form-control" name="country_name" id="country_id">
                        <option value="">{{trans('cities.select')}}</option>
                        @foreach($countries as $country)
                          <option value="{{$country->id}}" @if($country_id == $country->id) selected @endif>{{$country->name}}</option>
                        @endforeach
                      </select>
                      @error('country_name')
                        <div class="help-block">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label id="state_name-error" for="state_name" class ="content-label">
                      {{trans('cities.state_name')}}
                      </label>
                      {!! Form::select('state_name',[],null,['class' => 'form-control','placeholder' => trans('cities.select'), 'id' => 'state_name']) !!}
                      @error('state_name')
                        <div class="help-block">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="city_name">{{trans('cities.name')}}</label>
                      <input class="form-control" placeholder="{{trans('cities.enter_city')}}" required="true" name="city_name" type="text" value="{{$city->name}}" id="name" required>
                       @error('city_name')
                        <div class="help-block">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                </div>
              </div>
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
<script type="text/javascript">

  $( document ).ready(function() {
    get_state_List('{{$country_id}}');
    setTimeout(function(){ 
      $("#country option[value='{{$country_id}}']").prop('selected', true);
      $("#state_name option[value='{{$city->state_id}}']").prop('selected', true);
    },400);
    
  });

    function get_state_List(country_id = null){
    var url = '{{ route("states.list", ["country_id" => ":country_id"]) }}';  
    if(country_id){
      $.ajax({
        type:"GET",
        url:url.replace(':country_id', country_id),
        success:function(res){   
        if(res.success == "1"){
          $("#state_name").empty()
          //$("#state").append('<option>Select</option>');
          $.each(res.data,function(key,value){
           $("#state_name").append('<option value="'+value.id + '">'+value.name+'</option>');
          });
        } else {
          toaster.error(res.message);
          $("#state_name").empty();
        }
         }
      });
    } else{
      $("#state_name").empty();
    }
  }

 $('#country_id').change(function(){
      var country_id = $(this).val();
      var url = '{{ route("states.list", ["country_id" => ":country_id"]) }}';  
      if(country_id){
        $.ajax({
            type:"GET",
            url:url.replace(':country_id', country_id),
            success:function(res){   
              if(res.success == "1"){
                $("#state_name").empty()
                // $("#sub_category").append('<option>'+'{{trans("services.select_sub_category")}}'+'</option>');
                $.each(res.data,function(key,value){
                    $("#state_name").append('<option value="'+value.id + '">'+value.name+'</option>');
                });
              } else {
                toaster.error(res.message);
                $("#state_name").empty();
              }
             }
          });
      } else{
        $("#state_name").empty();
      }      
    });
</script>
@endsection