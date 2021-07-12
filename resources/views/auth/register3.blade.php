@extends('customer.layouts.auth')
@section('css')
<link rel="stylesheet" href="{{asset('adminAsset/plugins/datetimepicker/bootstrap-datetimepicker.min.css')}}">
<link rel="stylesheet" href="{{asset('css/toastr.min.css')}}" />
<style type="text/css">
    .content-lable-astrisk::after {
        content: "*";
        color: red;
    } 
    .working_hours_title { font-size: 20px  }
    .day {
      height: 25px;
    }

    #label {
        float: left;
        min-width: 80px; 
    }
    .start_time, .end_time {border:none; width: 80%; border-bottom: 1px dotted #000;}
    .day_name {line-height: 33px}
    .day_row {margin-bottom: 20px}

    <?php if(config('app.locales')[config('app.locale')]['dir'] == 'rtl'){ ?>
      /*on off days*/
      .onoffswitch {
        float: right;
        position: relative; width: 90px;
        -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
      }
      .onoffswitch-checkbox {
          position: absolute;
          opacity: 0;
          pointer-events: none;
      }
      .onoffswitch-label {
          display: block; overflow: hidden; cursor: pointer;
          border: 2px solid #999999; border-radius: 20px;
      }
      .onoffswitch-inner {
          display: block; width: 200%; margin-right: -100%;
          transition: margin 0.3s ease-in 0s;
      }
      .onoffswitch-inner:before, .onoffswitch-inner:after {
          display: block; float: right; width: 50%; height: 30px; padding: 0; line-height: 30px;
          font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
          box-sizing: border-box;
      }
      .onoffswitch-inner:before {
          content: "{{trans('common.on')}}";
          padding-left: 10px;
          background-color: #34A7C1; color: #FFFFFF;
          text-align: left; 
      }
      .onoffswitch-inner:after {
        content: "{{trans('common.off')}}";
        padding-right: 10px;
          background-color: #EEEEEE; color: #999999;
          text-align: right;
          
      }
      .onoffswitch-switch {
          display: block; width: 20px; height: 20px; margin: 7px;
          background: #FFFFFF;
          position: absolute; top: 0; bottom: 0;
          right: 56px;
          border: 2px solid #999999; border-radius: 20px;
          transition: all 0.3s ease-in 0s; 
      }

      .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
          margin-right: 0;
      }
      .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
          right: 0px; 
      }
    <?php } else { ?>
    .onoffswitch {
      float: right;
      position: relative; width: 90px;
      -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
    }
    .onoffswitch-checkbox {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .onoffswitch-label {
        display: block; overflow: hidden; cursor: pointer;
        border: 2px solid #999999; border-radius: 20px;
    }
    .onoffswitch-inner {
        display: block; width: 200%; margin-left: -100%;
        transition: margin 0.3s ease-in 0s;
    }
    .onoffswitch-inner:before, .onoffswitch-inner:after {
        display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
        font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
        box-sizing: border-box;
    }
    .onoffswitch-inner:before {
        content: "{{trans('common.on')}}";
        padding-left: 10px;
        background-color: #34A7C1; color: #FFFFFF;
    }
    .onoffswitch-inner:after {
        content: "{{trans('common.off')}}";
        padding-right: 10px;
        background-color: #EEEEEE; color: #999999;
        text-align: right;
    }
    .onoffswitch-switch {
        display: block; width: 20px; height: 20px; margin: 7px;
        background: #FFFFFF;
        position: absolute; top: 0; bottom: 0;
        right: 56px;
        border: 2px solid #999999; border-radius: 20px;
        transition: all 0.3s ease-in 0s; 
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
        margin-left: 0;
    }
    .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
        right: 0px; 
    }
    <?php } ?>
</style>
@endsection
@section('content')
<section class="section mt-60">
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
  <div class="container">   
  	<div class="row">
  		<div class="col-12">
  			<div class="step-box">
  				<h1>Welcome,</h1>
          @if(auth()->user()->user_type == 'vendor')
  				<span> {{trans('auth.step_3_title')}} </span>
          @else
          <span>{{trans('auth.step_2_individual_title')}}</span>
          @endif
  			</div>
  		</div>
  	</div>
  	<div class="row align-items-center justify-content-center">
  		<div class="col-12 col-lg-8">
    	  {!! Form::open(['route' => ['store_step3'],'method' => 'PUT','enctype'=>'multipart/form-data']) !!}
          @csrf
            <div class="row">
              <div class="col-12">
                  <div class="store-timing">
                    <h5 class="step3title">{{trans('auth.store_timings')}}</h5>
                    <a href="#" data-toggle="modal" data-target="#addtime"><span><i class="far fa-clock"></i></span>{{trans('auth.add')}}</a>
                  </div>
              </div>
            </div>
            <div class="row">
              @foreach(config('app.locales') as $lk=>$lv)
                <div class="col-12">
                  <div class="form-group @error('store_description:'.$lk) ? has-error : ''@enderror">
                    <label for="store_description:{{$lk}}"><h5 class="step3title content-lable-astrisk">{{trans('auth.about_us_'.$lk)}}</h5></label>
                    <textarea class="form-control @error('about_us:'.$lk) is-invalid @enderror" minlength="2" maxlength="255"  rows="5" placeholder="{{trans('auth.about_us_'.$lk)}}" name="about_us:{{$lk}}">{{old('about_us:'.$lk)}}</textarea>
                    @error('about_us:'.$lk)
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ @$errors->first('about_us:'.$lk) }}</strong>
                      </span>
                    @enderror
                  </div>  
                </div>
              @endforeach    
            </div>  
            @if(auth()->user()->user_type == 'vendor')     
            <div class="row mt-3">
              <div class="col-12">
                <label for="registration_certificate"><h5 class="step3title content-lable-astrisk">{{trans('auth.register_certificate')}}</h5></label>
              </div>
              <div class="col-12 mt-3">
                <div id="reg" class="dropzone">
                  <div class="fallback">
                    <input name="registration_certificate[]" type ='file'  multiple>
                  </div>
                </div>
              </div>  
            </div>
            <div class="row mt-3">
              <div class="col-12">
                <label for="company_certificate"><h5 class="step3title content-lable-astrisk">{{trans('auth.company_certificate')}}</h5></label>
              </div>
              <div class="col-12 mt-3">
                <div id="com" class="dropzone">
                  <div class="fallback">
                    <input name="company_certificate[]" type ='file' accept="image/*" multiple>
                  </div>
                </div>
              </div>
            </div>
            @elseif(auth()->user()->user_type == 'individual')
            <div class="row mt-3">
              <div class="col-12">
                <label for="id_certificates"><h5 class="step3title content-lable-astrisk">{{trans('auth.id_certificates')}}</h5></label>
              </div>
              <div class="col-12 mt-3">
                <div id="id_cer" class="dropzone">
                  <div class="fallback">
                    <input name="id_certificates[]" type ='file' multiple>   
                  </div>
                </div>
              </div> 
            </div>
            <div class="row mt-3">
              <div class="col-12">
                 <label for="citizen_certificates"><h5 class="step3title content-lable-astrisk">{{trans('auth.citizen_certificates')}}</h5></label>
              </div>
              <div class="col-12 mt-3">
                <div id="citizen" class="dropzone">
                  <div class="fallback">
                    <input name="citizen_certificates[]" type ='file' multiple >   
                  </div>
                </div>
              </div> 
            </div>
            @endif 
            <div class="row">
              <div class="col-12 mt-5">
                <div class="step-action">
                    <button type="submit" class="theme-btn">{{trans('common.submit')}}</button>
                </div>
              </div>
            </div>
        {{ Form::close() }}
  		</div>
  	</div>
  </div>
</section>
<div class="modal fade" id="addtime" tabindex="-1" role="dialog" aria-labelledby="addtime" aria-hidden="true">
{{ Form::open(array('method'=>'post','route' => 'hours','id'=>'workinghour','onsubmit' =>"return validate_working_hours()"))}}
    <div class="modal-dialog modal-lg addtime-modal" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="review">{{trans('vendors.working_hours_title')}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
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
                  <div class="box box-info">
                      <div class="box-body">
                          <div class="modal-body">
                            <div id="hourForm">
                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.0')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Sunday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_sun"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[0]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_sun"  class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[0]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                    <input type="hidden" id="onoffval_sun" name="wh_status[]" value="@if(@$working_hours[0]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[0]->status == 1) checked @endif class="onoffswitch-checkbox" id="myonoffswitch_sun" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_sun">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>
                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.1')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Monday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_mon"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[1]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_mon"  class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[1]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                      <input type="hidden" id="onoffval_mon" name="wh_status[]" value="@if(@$working_hours[1]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[1]->status == 1) checked @endif  class="onoffswitch-checkbox" id="myonoffswitch_mon" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_mon">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>
                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.2')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Tuesday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_tue"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[2]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_tue"  class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[2]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                      <input type="hidden" id="onoffval_tue" name="wh_status[]" value="@if(@$working_hours[2]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[2]->status == 1) checked @endif  class="onoffswitch-checkbox" id="myonoffswitch_tue" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_tue">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>
                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.3')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Wednesday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_wed"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[3]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_wed"  class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[3]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                      <input type="hidden" id="onoffval_wed" name="wh_status[]" value="@if(@$working_hours[3]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[3]->status == 1) checked @endif  class="onoffswitch-checkbox" id="myonoffswitch_wed" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_wed">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>

                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.4')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Thursday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_thu"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[4]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_thu"   class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[4]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                      <input type="hidden" id="onoffval_thu" name="wh_status[]" value="@if(@$working_hours[4]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[4]->status == 1) checked @endif  class="onoffswitch-checkbox" id="myonoffswitch_thu" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_thu">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>
                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.5')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Friday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_fri"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[5]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_fri"  class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[5]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                      <input type="hidden" id="onoffval_fri" name="wh_status[]" value="@if(@$working_hours[5]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[5]->status == 1) checked @endif  class="onoffswitch-checkbox" id="myonoffswitch_fri" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_fri">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>
                              <div class="row day_row">
                                <div class="col-md-2">
                                  <span class="day_name">{{trans('common.week_days.6')}}</span>  
                                  <input type="hidden" name="working_day[]" value="Saturday"> 
                                </div>
                                <div class="col-md-3">
                                  <input name="start_time[]" id="start_time_sat"  class="form-control start_time" type="text" value="{{old('start_time', @$working_hours[6]->start_time)}}">  
                                </div>
                                <div class="col-md-3">
                                  <input name="end_time[]" id="end_time_sat"  class="form-control end_time" type="text" value="{{old('end_time', @$working_hours[6]->end_time)}}" >
                                </div>
                                <div class="col-md-4">
                                  <div class="onoffswitch">
                                      <input type="hidden" id="onoffval_sat" name="wh_status[]" value="@if(@$working_hours[6]->status == 1) 1 @else 0 @endif">
                                      <input type="checkbox" @if(@$working_hours[6]->status == 1) checked @endif  class="onoffswitch-checkbox" id="myonoffswitch_sat" value="1" tabindex="0">
                                      <label class="onoffswitch-label" for="myonoffswitch_sat">
                                          <span class="onoffswitch-inner"></span>
                                          <span class="onoffswitch-switch"></span>
                                      </label>
                                    </div> 
                                </div>
                              </div>     
                            </div>     
                          </div>
                          <div class="modal-footer">
                              <button type="submit" class="btn btn-info btn-fill btn-wd">{{trans('common.submit')}}</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </section>
      </div>
    </div>  
  {{ Form::close() }}
</div>
@endsection
@section('js')
<script src="{{ asset('adminAsset/plugins/datetimepicker/moment-with-locales.js') }}"></script>
<script src="{{ asset('adminAsset/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>
<script src="{{asset('js/toastr.min.js')}}"></script>
<script type="text/javascript">
    @if(Session::has('success'))
      toastr.success("{{ Session::get('success') }}");
    @elseif(Session::has('error'))
      toastr.error("{{ Session::get('error') }}");
    @elseif(Session::has('warning'))
      toastr.warning("{{ Session::get('warning') }}");
    @elseif(Session::has('info'))
      toastr.info("{{ Session::get('info') }}");
    @endif
     
$('.alert-danger').delay(5000).fadeOut();

   function isNumber(evt) {
          var iKeyCode = (evt.which) ? evt.which : evt.keyCode
          if (iKeyCode != 46 && iKeyCode > 31 && (iKeyCode < 48 || iKeyCode > 57))
              return false;

          return true;
      }
</script>

<script>
   $("div#reg").dropzone({  
    url: "{{url('/store_certificate')}}",
    params: {'certificates':'registration_certificate'},
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    addRemoveLinks : true,
    maxFilesize: 5,
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Drop files <span class="font-xs">to upload</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (Or Click)</h4></span>',
    dictResponseError: 'Error uploading file!',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
});

$("div#com").dropzone({  
    url: "{{url('/store_certificate')}}",
    params: {'certificates':'company_certificate'},
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    addRemoveLinks : true,
    maxFilesize: 5,
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Drop files <span class="font-xs">to upload</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (Or Click)</h4></span>',
    dictResponseError: 'Error uploading file!',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
});
$("div#id_cer").dropzone({  
    url: "{{url('/store_certificate')}}",
    params: {'certificates':'id_certificate'},
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    addRemoveLinks : true,
    maxFilesize: 5,
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Drop files <span class="font-xs">to upload</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (Or Click)</h4></span>',
    dictResponseError: 'Error uploading file!',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
});
$("div#citizen").dropzone({  
    url: "{{url('/store_certificate')}}",
    params: {'certificates':'citizen_certificate'},
    acceptedFiles: ".jpeg,.jpg,.png,.gif",
    addRemoveLinks : true,
    maxFilesize: 5,
    dictDefaultMessage: '<span class="text-center"><span class="font-lg visible-xs-block visible-sm-block visible-lg-block"><span class="font-lg"><i class="fa fa-caret-right text-danger"></i> Drop files <span class="font-xs">to upload</span></span><span>&nbsp&nbsp<h4 class="display-inline"> (Or Click)</h4></span>',
    dictResponseError: 'Error uploading file!',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
});
$( document ).ready(function() {
    $('.start_time,.end_time').attr('autocomplete','off');
      $('.start_time').datetimepicker({
        icons:
        {
          up: 'fa fa-angle-up',
          down: 'fa fa-angle-down'
        },
        format: 'hh:mm a',
        stepping: 30
      });
      $('.end_time').datetimepicker({
        icons:
        {
          up: 'fa fa-angle-up',
          down: 'fa fa-angle-down'
        },
        format: 'hh:mm a',
        stepping: 30
      });
  });

$( document ).ready(function() {
  $('.onoffswitch-checkbox').change(function(){
    var onoff = $(this).is(":checked");
    $(this).prev().val('0');
    if(onoff){
      $(this).prev().val('1');
    }
    // alert($(this).attr('id')+onoff);
  })

  // checked button Validation
  $.each(['sun','mon','tue','wed','thu','fri','sat' ],function(index,value) {
    $('#myonoffswitch_'+value).change(function() {
      if(this.checked) {
        $('#start_time_'+value).prop('required',true);
        $('#end_time_'+value).prop('required',true);
      } else {
        $('#start_time_'+value).prop('required',false);
        $('#end_time_'+value).prop('required',false);
      }
    });

    if($('#myonoffswitch_'+value).prop('checked') == false) {
      $('#start_time_'+value).val('');
      $('#end_time_'+value).val('');
    }
  });

})

function validate_working_hours() {
  if($('#onoffval_sun').val() == 0 && $('#onoffval_mon').val() == 0 && $('#onoffval_tue').val() == 0 && $('#onoffval_wed').val() == 0 && $('#onoffval_thu').val() == 0 && $('#onoffval_fri').val() == 0 && $('#onoffval_sat').val() == 0) {
      // alert("{{trans('working_hours.choose_atleast_one')}}");
      // return false
    }

  $('#start_time_sun,#end_time_sun,#start_time_mon,#end_time_mon,#start_time_tue,#end_time_tue,#start_time_wed,#end_time_wed,#start_time_thu,#end_time_thu,#start_time_fri,#end_time_fri,#start_time_sat,#end_time_sat').css('background', 'none');
  // return true;
  var time_flag = false;
  $.each(['sun','mon','tue','wed','thu','fri','sat' ],function(index,day)
  {
    if($("#start_time_"+day).prop('required') && $("#end_time_"+day).prop('required') && $("#myonoffswitch_"+day).prop('checked') == true && minFromMidnight($('#start_time_'+day).val()) >= minFromMidnight($('#end_time_'+day).val())){
      time_flag = true;
      $('#start_time_'+day).css('background', 'yellow');
      $('#end_time_'+day).css('background', 'yellow');
    }
  })

  // if(time_flag){
  //   alert("{{trans('working_hours.time_not_correct')}}");
  //   return false
  // }
  // return true;
}

function minFromMidnight(tm){
 var ampm= tm.substr(-2)
 var clk = tm.substr(0, 5);
 var m  = parseInt(clk.match(/\d+$/)[0], 10);
 var h  = parseInt(clk.match(/^\d+/)[0], 10);
 h += (ampm.match(/pm/i))? 12: 0;
 return h*60+m;
}

$('#workinghour').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        type: "POST",
        url: "{{ route('hours') }}",
        data: $(this).serialize(),
        beforeSend: function () {
                    $('#send_comment').html('Sending..<span id="loader" style="visibility: hidden;"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i><span class="sr-only">a</span></span>');
                    $('#loader').css('visibility', 'visible');
        },
        success: function (data) {
                  $('#loader').css('visibility', 'hidden');
                  if(data.type == 'error'){
                    toastr.error(data.error);
                  }else{
                    $('#addtime').modal('hide');
                    toastr.success(data.message);
                  }
                },
        error: function () {
            $('#loader').css('visibility', 'hidden');
              toastr.error('Somethink Wrong');
            }
    });
});

</script>
@endsection