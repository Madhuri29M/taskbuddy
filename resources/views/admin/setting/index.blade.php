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
<div class="content-wrapper">
<section class="content-header">
  <h1>
    {{ trans('setting.list') }}
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i>{{ trans('setting.home')}}</a></li>
    <li><a href="{{route('setting.index')}}">{{ trans('setting.settings')}}</a></li>
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
            <div class="box-body">
                <form action="{{route('setting.update')}}" method="POST" id="setting" enctype="multipart/form-data">
                    @csrf
                  <div class="modal-body">
                    @foreach($settings as $k=>$v)
                      @if($k == 'app_name')
                          <div class="col-md-6">
                            <div class="form-group">
                                <label class="content-label" >{{ trans('setting.'.strtolower($k) ) }}  </label>             
                                  <input type="text" class="form-control" name="{{$k}}" placeholder="{{trans('setting.'.strtolower($k))}}" aria-describedby="basic-addon1" value="{{$v}}" required>

                                 @error(strtolower($k))
                                     <div class="help-block">{{ $message }}</div>
                                  @enderror
                            </div>
                          </div>
                      @elseif($k == 'google_map_api_key')
                        <div class="col-md-6">
                            <div class="form-group">
                              <label class="content-label">{{ trans('setting.'.strtolower($k)) }}</label>             
                                <input type="text" class="form-control" name="{{$k}}" placeholder="{{trans('setting.'.strtolower($k))}}" aria-describedby="basic-addon1" value="{{$v}}" required>
                                @error(strtolower($k))
                                 <div class="help-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                      @else
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="content" class="content-label">{{trans('setting.'.strtolower($k))}}</label>
                          <input class="form-control" minlength="2" maxlength="255" placeholder="{{trans('setting.'.strtolower($k))}}" name="{{$k}}" type="text" value="{{$v}}">
                          @error(strtolower($k))
                            <div class="help-block">{{ $message }}</div>
                          @enderror
                        </div>
                      </div>
                    @endif 
                    @endforeach
                  </div>
                  
                  <div class="modal-footer">
                    <button id="edit_btn" type="submit" class="btn btn-info btn-fill btn-wd">{{ trans('setting.save')}}</button>
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

<script>
  $(document).ready(function () {

     $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
 
        $('#image').change(function(){
          
            let reader = new FileReader();
            reader.onload = (e) => { 
              $('#preview').attr('src', e.target.result); 
            }
            reader.readAsDataURL(this.files[0]); 
      });

    $('#setting').validate({
        errorClass: ".alert-danger",
        rules: {

            app_name: {
                required: true,
            },
            app_short_name: {
                required: true, 
            },
            email_username:{
                required: true,
                email: true
            },
            email_password:{
                required: true,
            },
            email_host:{
                required: true,
            },
            email_port:{
                required: true,
                digits: true,
            },
            email_encryption:{
                required: true,
            },
            email_from_name:{
                required: true,
            },
            email_from_address:{
                required: true,
                email: true,
            },
            commission_rate:{
                required: true,
                digits: true,
            },
            cancel_charge:{
                required: true,
                digits: true,
            }
        }
    });
});
</script>
@endsection

