@extends('customer.layouts.auth')
@section('css')
<link rel="stylesheet" href="{{asset('css/toastr.min.css')}}" />
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
  				<span>{{trans('auth.step_2_title')}}</span>
  			</div>
  		</div>
  	</div>
  	<div class="row align-items-center justify-content-center">
  		<div class="col-12 col-lg-8">
  			<div class="step-form">
  			  <form method="POST" action="{{ route('store_step2') }}" accept-charset="UTF-8" enctype="multipart/form-data">
            @csrf
            <div class="row">
              @foreach(config('app.locales') as $lk=>$lv)
                <div class="col-12 col-lg-6 store_name">
                  <label for="store_name" class="content-lable-astrisk">{{trans('auth.store_name_'.$lk)}}</label>
                    <div class = 'store_name @if(old("register_as") == "individual") hidden @endif'>
                      <div class="form-group has-feedback">
                          <input class="form-control @error('store_name:'.$lk) is-invalid @enderror" placeholder="{{ trans('auth.store_name_'.$lk) }}" type="text" name="store_name:{{$lk}}" value="{{ old('store_name:'.$lk,$profile->translate($lk)->store_name) }}" id="store_name_{{$lk}}" required >
                          @error('store_name:'.$lk)
                              <span class="invalid-feedback" role="alert">
                                  <strong>{{ @$errors->first('store_name:'.$lk) }}</strong>
                              </span>
                          @enderror
                      </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                  <label for="store_address" class="content-lable-astrisk">{{trans('auth.store_address_'.$lk)}}</label>
                  <div class="form-group">
                      <textarea  placeholder="{{ trans('auth.store_address_'.$lk) }}" class="form-control @error('store_address:'.$lk) is-invalid @enderror"  name="store_address:{{$lk}}" required autocomplete="store_address">{{old('store_address:'.$lk,$profile->translate($lk)->store_address)}}</textarea>
                      @error('store_address:'.$lk)
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ @$errors->first('store_address:'.$lk) }}</strong>
                          </span>
                      @enderror
                  </div>
                </div>
              @endforeach
              <div class="col-12 col-lg-6">
                <div class="form-group">
                  <label for="store_email" class="content-lable-astrisk">{{trans('auth.store_email')}}</label>
                  <input type="email" class="form-control @error('store_email') is-invalid @enderror" name="store_email"  value="{{ old('store_email',$profile->store_email) }}"  placeholder="{{trans('auth.store_email')}}" required>
                  @error('store_email')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
              </div>
              <div class="col-12 col-lg-6">
              	<div class="form-group">
                    <label for="store_contact" class="content-lable-astrisk">{{trans('auth.store_contact')}}</label>
                    <input id="store_contact" class="form-control @error('store_contact') is-invalid @enderror" name="store_contact" value="{{ old('store_contact',$profile->store_email)}}" type="text"  maxlength = "50"  placeholder="{{trans('auth.store_contact')}}" required>
                    @error('store_contact')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                  </div>
              </div>
              <div class="col-12 col-lg-6 individual_instagram_image">
                <div class="form-group">
                  <label for="instagram_id" class="content-lablel">{{trans('auth.instagram_id')}}</label>
                  <input type="instagram_id" class="form-control @error('instagram_id') is-invalid @enderror" name="instagram_id" value="{{ old('instagram_id',$profile->instagram_id) }}"  autocomplete="instagram_id" autofocus placeholder="@username">
                  <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                  @error('instagram_id')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>
  							<!-- <div class="map-a">
  								<a href="#" data-toggle="modal" data-target="#searchonmap"><img src="img/loc-point.png" alt=""></a>
  							</div>
  						</div> -->
              <!-- </div> -->
              <div class="col-12">
                <h5 class="banner-label">{{trans('auth.store_logo_image')}}</h5>
                <div class="step-banner">
                  <div class="store-banner-img">
                    <img src="{{asset('customer/img/upload-img.svg')}}" alt="" class="img-fluid">
                      </div>
                      <!-- <div class="upload-btn-wrapper"> -->
                            <!-- <button class="upload-btn">Upload</button> -->
                            <input type="file" class=" @error('store_logo_image') is-invalid @enderror" name="store_logo_image"  accept="image/*">
                      <!-- </div> -->
                      @error('store_logo_image')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>
              <div class="col-12">
              	<h5 class="banner-label">{{trans('auth.store_main_banner_image')}}</h5>
              	<div class="step-banner">
              		<div class="store-banner-img">
                    <img src="{{asset('customer/img/upload-img.svg')}}" alt="" class="img-fluid">
                      </div>
                      <!-- <div class="upload-btn-wrapper"> -->
                            <!-- <button class="upload-btn">Upload</button> -->
                            <input type="file" class=" @error('store_banner_image') is-invalid @enderror" name="store_banner_image"  accept="image/*">
                      <!-- </div> -->
                      @error('store_banner_image')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
              	</div>
              </div>
              
              <div class="col-12">
              	<div class="tag-box">
              		<div class="tag-head">
              			<h5>{{trans('auth.tags')}}<span class="content-lable-astrisk"></span></h5>
              			<a href="#" data-toggle="modal" data-target="#addtag">Add Tag</a>
              		</div>
              		<div class="tag-list">
                    @foreach($tags_categories as $category)
                      <span class="tag_list_selected" data_id="{{$category->id}}" >
                        {{$category->category_name}}
                      </span>
                    @endforeach
                    
              	  </div>
              </div>
              <div class="row">
                <div class="col-md-9">
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
                        <input type="text" class="mapControls" name="lattitude" id="lat-span" value="{{old('lattitude', $profile->lattitude)}}">
                        @error('lattitude')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                    <div class="form-group">
                      <label class="content-lable-astrisk">{{trans('vendors.longitude')}}</label>
                      <br>
                      <input type="text" class="mapControls @error('longitude') is-invalid @enderror" name="longitude" id="lon-span" value="{{old('longitude', $profile->longitude)}}" >
                      @error('longitude')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <!-- LONGITUDE -->
                   <input type="hidden" name="tags" id="tags_selected">
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
  </div>
</section>
<div class="modal fade" id="searchonmap" tabindex="-1" role="dialog" aria-labelledby="searchonmap" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="review">Search Your loaction</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="seach-location-box">
          <div class="search-location-bar">
            <form action="">
              <div class="search-box p-1 rounded rounded-pill shadow-sm">
                <div class="input-group">
                  <input type="search" placeholder="Search Here..." aria-describedby="button-addon1" class="form-control border-0">
                  <div class="input-group-append">
                    <button id="button-addon1" type="submit" class="btn btn-link"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="row">
            <div class="col-12 col-md-7">
              <div class="drag-map">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d235013.70717984336!2d72.43965621445852!3d23.020497769509888!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e848aba5bd449%3A0x4fcedd11614f6516!2sAhmedabad%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1614598479877!5m2!1sen!2sin" width="100%" height="350px" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
              </div>
            </div>
            <div class="col-12 col-md-5">
              <form class="addlocation-form">
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="house-street">House/Street</label>
                      <input type="text" class="form-control" id="house-street" placeholder="3, Royal Villa">
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="address">Address</label>
                      <textarea class="form-control" id="address" rows="3"></textarea>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-group">
                      <label for="city">City</label>
                      <select class="form-control" id="city">
                        <option>Riyadh</option>
                        <option>Riyadh</option>
                        <option>Riyadh</option>
                        <option>Riyadh</option>
                        <option>Riyadh</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-12">
                    <button type="submit" class="theme-btn">SAVE</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="addtag" tabindex="-1" role="dialog" aria-labelledby="addtag" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="review">Select Your Tags</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      	<div class="tagmodal">
      		<ul id='myid'>
            @foreach($tags_categories as $category)
              <li class="tag_list" data-tag data_id="{{$category->id}}"><span>{{$category->category_name}}</span></li>
            @endforeach
           
      		</ul>
      	</div> 
      </div>
    </div>
  </div>
</div>
@endsection
@section('js')
<!--Toaster JS-->
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
  $(document).ready(function () {
    $('.tag_list_selected').hide();
    // alert();
    // var tagid = 2;
    // $('.tag_list_selected[data_id='+tagid+']').show();
    $('#tags_selected').val("");
    var tags_selected = '';
              $("ul[id*=myid] li").click(function () {
                  set_tags();
              });
      function set_tags() {
          tags_selected = '';
          $('.tag_list').each(function(){
            var tag_id = $(this).attr('data_id');
            if($(this).hasClass('active')){
              tags_selected += tag_id+','; 
              $('#tags_selected').val(tags_selected);
              $('.tag_list_selected[data_id='+tag_id+']').show();
            } else {
              $('.tag_list_selected[data_id='+tag_id+']').hide();
            }
          })
      }          
  });

    /*code start for autocomplete map*/
  function initMap() {
      @php
          $init_latitude = ($profile->lattitude) ?  $profile->lattitude : App\Models\Setting::get('latitude');
          $init_longitude = ($profile->longitude) ?  $profile->longitude : App\Models\Setting::get('longitude');
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