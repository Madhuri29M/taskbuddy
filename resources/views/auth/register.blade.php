@extends('customer.layouts.auth')
@section('css')
<style>
  .content-lable-astrisk::after {
      content: "*";
      color: red;
  } 
  #map {
      width: 100%;
      height: 400px;
    }
    .mapControls {
      margin-top: 10px;
      border: 1px solid transparent;
      border-radius: 2px 0 0 2px;
      box-sizing: border-box;
      -moz-box-sizing: border-box;
      height: 32px;
      outline: none;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
    }
    #searchMapInput {
      background-color: #fff;
      font-family: Roboto;
      font-size: 15px;
      font-weight: 300;
      margin-left: 12px;
      padding: 0 11px 0 13px;
      text-overflow: ellipsis;
      width: 50%;
    }
    #searchMapInput:focus {
      border-color: #4d90fe;
    }
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
          <h1>{{trans('auth.welcome')}},</h1>
          <span> {{trans('auth.step_1_title')}}</span>
        </div>
      </div>
    </div>
    <div class="row align-items-center justify-content-center">
      <div class="col-12 col-lg-8">
        <div class="step-form">
          <form method="POST" action="{{ route('register') }}" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
              <div class="row">
                <div class="col-12 col-lg-6">
                  <div class="form-group has-feedback">
                    <label for="registeras" class="content-lable-astrisk">{{trans('auth.register_as')}}</label>
                    <select class="form-control @error('register_as') is-invalid @enderror" name="register_as" id='register_as'   required autocomplete="register_as" autofocus placeholder="{{trans('auth.vendor_type')}}">
                      <option value="">{{trans('auth.register_as')}}</option>
                        <option value="vendor" @if(old('register_as') == 'vendor') selected @endif >{{trans('auth.store')}}</option>
                        <option value="individual" @if(old('register_as') == 'individual') selected @endif >{{trans('auth.individual')}}
                      </option>
                    </select>
                  </div>
                  @error('register_as')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="col-12 col-lg-6 full_name_vendor">
                  <div class="form-group">
                    <label for="fullname" class="content-lable-astrisk">{{trans('auth.full_name')}}</label>
                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{old('full_name')}}" autocomplete="full_name" autofocus placeholder="{{trans('auth.full_name')}}">  
                    @error('full_name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                </div>  
                @foreach(config('app.locales') as $lk=>$lv)                
                  <div class="col-12 col-lg-6 full_name_individual">
                    <div class="form-group">
                      <label for="fullname" class="content-lable-astrisk">{{ trans('auth.full_name_'.$lk) }}</label>
                      <input class="form-control @error('full_name:'.$lk) is-invalid @enderror" placeholder="{{ trans('auth.full_name_'.$lk) }}" type="text" name="full_name:{{$lk}}" value="{{ old('full_name:'.$lk) }}" id="full_name_{{$lk}}">
                      @error('full_name:'.$lk)
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ @$errors->first('full_name:'.$lk) }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>        
             <!--      <div class="col-12 col-lg-6">
                    <div class="form-group">
                        <label for="fullname" class="content-lable-astrisk">{{ trans('auth.store_name_'.$lk) }}</label>
                        <input class="form-control @error('store_name:{{$lk}}') is-invalid @enderror" placeholder="{{ trans('auth.store_name_'.$lk) }}*" type="text" name="store_name:{{$lk}}" value="{{ old('store_name:'.$lk) }}" id="store_name_{{$lk}}">
                        @error('store_name:'.$lk)
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ @$errors->first('store_name:'.$lk) }}</strong>
                            </span>
                        @enderror
                    </div>
                  </div> -->
                  <div class="col-12 col-lg-6 address_individual">
                    <div class="form-group">
                      <label for="address" class="content-lable-astrisk">{{ trans('auth.address_'.$lk) }}</label>
                      <textarea  placeholder="{{ trans('auth.address_'.$lk) }}" class="form-control @error('address:'.$lk) is-invalid @enderror"  name="address:{{$lk}}"  autocomplete="address">{{old('address:'.$lk) }}</textarea>
                      @error('address:'.$lk)
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ @$errors->first('address:'.$lk) }}</strong>
                          </span>
                      @enderror
                    </div>
                  </div>
                @endforeach
                <div class="col-12 col-lg-6">
                  <div class="form-group">
                      <label class="content-lable-astrisk">{{trans('auth.mobile_number')}}</label>
                  <!--     <select class="form-control pull-left" style="width: 25%;">
                          <option>{{$country->country_code}}</option>
                      </select> -->
                      <input class="form-control @error('mobile_number') is-invalid @enderror" name="mobile_number" placeholder="{{trans('auth.mobile_number')}}" required value="{{ old('mobile_number') }}" type="tel" maxlength = "10">
                      @error('mobile_number')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                  </div>
                </div>
                <div class="col-12 col-lg-6 individual_instagram_image">
                  <div class="form-group">
                    <label for="instagram_id" class="content-label">{{trans('auth.instagram_id')}}</label>
                    <input type="instagram_id" class="form-control @error('instagram_id') is-invalid @enderror" name="instagram_id" value="{{ old('instagram_id') }}"  autocomplete="instagram_id" autofocus placeholder="@username">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @error('instagram_id')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                </div>
                <div class="col-12 col-lg-6 individual_instagram_image">
                  <div class="form-group">
                      <label for="profile_image" class="content-lable-astrisk">{{trans('auth.profile_image')}}</label>     
                      <input class="form-control @error('profile_image') is-invalid @enderror" type="file" name="profile_image" placeholder="Choose image"  id="image"  accept="image/*" >
                      @error('profile_image')
                          <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                  </div>
                </div>
                <div class="col-12 col-lg-6">
                  <div class="form-group">
                    <label for="email" class="content-lable-astrisk">{{trans('auth.email')}}</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="{{trans('auth.email')}}">
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                </div>
                <div class="col-12 col-lg-6">
                  <div class="form-group">
                        <label for="password" class="content-lable-astrisk">{{trans('auth.password')}}</label>
                        <input type="password" required placeholder="{{trans('auth.password')}}" class="form-control @error('password') is-invalid @enderror" name="password">
                        @error('password')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>
                <div class="col-12 col-lg-6">
                      <div class="form-group">
                            <label for="password_confirmation" class="content-lable-astrisk">{{trans('auth.confirm_password')}}</label>
                            <input type="password" required class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="{{trans('auth.confirm_password')}}" name="password_confirmation">
                          @error('password_confirmation')
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                </div>
              </div>
              <div id="individual_map" class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="content-lable-astrisk">{{trans('vendors.enter_address')}}</label>
                        <input id="searchMapInput" class="mapControls" type="text" placeholder="Enter a location">
                        <div id="map"></div>
                    </div> 
                </div>
                <!--LATTITUDE  -->
                <div class="col-md-3 ">
                  <div class="form-group">
                      <label class="content-lable-astrisk">{{trans('vendors.lattitude')}}</label>
                      <br>
                      <input type="text" class="mapControls" name="lattitude" id="lat-span" value="{{old('lattitude')}}">
                      @error('lattitude')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                  </div>
                </div>
                <!-- LONGITUDE -->
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="content-lable-astrisk">{{trans('vendors.longitude')}}</label>
                    <br>
                    <input type="text" class="mapControls @error('longitude') is-invalid @enderror" name="longitude" id="lon-span" value="{{old('longitude')}}" >
                    @error('longitude')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <input type="hidden" name="tags" id="tags_selected">
              </div>
              <div class="col-12">
                <div class="form-check">
                  <input class="form-check-input" required type="checkbox" value="1" name="is_terms">
                  <label class="form-check-label terms-label" for="terms">{{trans('auth.agree_terms_conditions')}} <a href="{{route('cms_page','terms_conditions')}}" target="_blank">{{trans('common.terms_conditon')}}</a></label><span class="content-lable-astrisk"></span>
                </div>
              </div>
              <div class="col-12 mt-5">
                <div class="step-action">
                  <button type="submit" class="theme-btn">{{trans('auth.next_step')}}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="back-arrow">
        <a href="{{route('login')}}"><span><i class="fas fa-long-arrow-alt-left"></i></span></a>
    </div>
  </div>
</section>
@endsection
@section('js')
<script>
  $('.multiselect-input').attr('autocomplete','off');

  $(".full_name_vendor").hide();
  $(".address_individual").hide();
  $(".full_name_individual").hide();
  $(".individual_instagram_image").hide();
  $("#individual_map").hide();

  $('#register_as').change(function(){
   var register_as =  $("#register_as option:selected").val();
   
      if(register_as == 'individual'){
        $(".full_name_individual").show();
        $(".address_individual").show();
        $(".individual_instagram_image").show();
        $(".full_name_vendor").hide();
        $("#individual_map").show();
      }else if(register_as == 'vendor'){
        $(".full_name_individual").hide();
        $(".address_individual").hide();
        $(".individual_instagram_image").hide();
        $(".full_name_vendor").show();
        $("#individual_map").hide();
      }else{
        $(".full_name_individual").hide();
        $(".address_individual").hide();
        $(".individual_instagram_image").hide();
        $(".full_name_vendor").hide();
        $(".individual_map").hide();

      }
  });

   $( document ).ready(function(){
      var value= "{{old('register_as')}}"
       if(value == 'individual'){
        $(".full_name_individual").show();
        $(".address_individual").show();
        $(".individual_instagram_image").show();
        $(".full_name_vendor").hide();
        $("#individual_map").show();
      }else if(value == 'vendor'){
        $(".full_name_individual").hide();
        $(".address_individual").hide();
        $(".individual_instagram_image").hide();
        $(".full_name_vendor").show();
        $("#individual_map").hide();
      }
    });


     /*code start for autocomplete map*/
    function initMap() {
        @php
            $init_latitude = App\Models\Setting::get('latitude');
            $init_longitude = App\Models\Setting::get('longitude');
        @endphp
        var map = new google.maps.Map(document.getElementById('map'), {
          center: {lat: <?=$init_latitude?>, lng: <?=$init_longitude?>},
          zoom: 13
        });
        var input = document.getElementById('searchMapInput');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
       
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
      
        var infowindow = new google.maps.InfoWindow();
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29),
            draggable:true,
            //animation: google.maps.Animation.DROP
        });
        // Pointer movemnt listener
          google.maps.event.addListener(marker, 'dragend', function(evt){
          $('#lat-span').val(evt.latLng.lat().toFixed(3));
          $('#lon-span').val(evt.latLng.lng().toFixed(3))
          }); 

        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
        
            /* If the place has a geometry, then present it on a map. */
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setIcon(({
                url: place.icon,
                size: new google.maps.Size(71, 71),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(17, 34),
                scaledSize: new google.maps.Size(35, 35)
            }));
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
          
            var address = '';
            if (place.address_components) {
                address = [
                  (place.address_components[0] && place.address_components[0].short_name || ''),
                  (place.address_components[1] && place.address_components[1].short_name || ''),
                  (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }
          
            infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
            infowindow.open(map, marker);
            
            /* Location details */
            $("textarea[name='address']").val(place.formatted_address);
            $('input[name="lattitude"]').val(place.geometry.location.lat())
            $('input[name="longitude"]').val(place.geometry.location.lng())
        });
    }
</script>
<?php 
    $google_map_api_key = App\Models\Setting::get('google_map_api_key');
?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&callback=initMap&key=<?php echo $google_map_api_key; ?>" async defer></script>
@endsection