<?php
$routename = Route::currentRouteName();
?>
<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      @php  

      $user =  

      request()->is('admin/user*') || request()->is('admin/c_message*')  ? 'block' : 'none' ;

      $customer =  
          request()->is('admin/customers*') || request()->is('admin/c_message*')  ? 'block' : 'none' ;
      $customer_tree =  
          request()->is('admin/customers*') || request()->is('admin/c_message*') ? 'menu-open' : 'none' ;

      $master =  

      request()->is('admin/country*') || request()->is('admin/country*') | request()->is('admin/states*') || request()->is('admin/cities*') || (request()->is('admin/category*')) ? 'block' : 'none' ;

      $master_tree =  

      request()->is('admin/country*') || request()->is('admin/country*') || request()->is('admin/states*') || request()->is('admin/cities*') || (request()->is('admin/category*')) ? 'menu-open' : 'none' ;

      $vndr_master =  
      (request()->is('admin/category*') || request()->is('admin/services*') || request()->is('admin/products*')) ? 'block' : 'none' ;

      $vndr_master_tree =  
      (request()->is('admin/category*') || request()->is('admin/services*') || request()->is('admin/products*')) ? 'menu-open' : 'none' ;

      $cms =  

       request()->is('admin/cms*')  ? 'block' : 'none' ;

      $cms_tree =  

      request()->is('admin/cms*') ? 'menu-open' : 'none' ;

      $offer_tree =  

        (request()->is('admin/offers*') ||  request()->is('admin/coupon_offers*')) ? 'menu-open' : 'none' ;

      $offer =  

        (request()->is('admin/offers*') ||  request()->is('admin/coupon_offers*')) ? 'block' : 'none' ;

      $profile =  
      request()->is('admin/business_profile*') || request()->is('admin/certificate*') || request()->is('admin/profile/working_hours*') || (request()->is('admin/bank_details*')) ? 'block' : 'none' ;

      $profile_tree =  
      request()->is('admin/business_profile*') || request()->is('admin/certificate*') || request()->is('admin/profile/working_hours*') || (request()->is('admin/bank_details*')) ? 'menu-open' : 'none' ;
     
      $cancel_reason_tree =  
          (request()->is('admin/reasons*') ||  request()->is('admin/cancel_reasons*')) ? 'menu-open' : 'none' ;
      $cancel_reasons =  
          (request()->is('admin/reasons*') ||  request()->is('admin/cancel_reasons*')) ? 'block' : 'none' ;

      $payout_tree =  
          (request()->is('admin/admin_payout*') ||  request()->is('admin/vendor_payouts*') || request()->is('admin/admin_profit*')) ? 'menu-open' : 'none' ;
      $payout =  
          (request()->is('admin/admin_payout*') ||  request()->is('admin/vendor_payouts*') || request()->is('admin/admin_profit*')) ? 'block' : 'none' ;

      $review =  

      request()->is('admin/partner_review*')  ? 'block' : 'none' ;

      $review_tree =  

      request()->is('admin/partner_review*') ? 'menu-open' : 'none' ;  

      @endphp
      
      @if(auth()->user()->user_type == 'admin' || auth()->user()->user_type == 'sub_admin')
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">{{ trans('sidebar.main_navigation') }}</li>
        <!-- <li class="active treeview menu-open"> -->
        <li class="@if($routename == 'home') active @endif">
          <a href="{{route('home')}}">
            <i class="fa fa-dashboard"></i> <span>{{ trans('sidebar.dashboard') }}</span>
            <span class="pull-right-container">
              <!-- <i class="fa fa-angle-left pull-right"></i> -->
            </span>
          </a>
        </li>
        @can('sub-admin-list')
        <li class="{{ (request()->is('admin/sub_admin*')) ? 'active' : '' }}">
          <a href="{{route('sub_admin.index')}}">
            <i class="fa fa-code-fork"></i> <span>{{ trans('sidebar.sub_admin') }}</span>
          </a>
        </li>
        @endcan
        @can('customer-list')
        <li class="treeview  {{ $customer_tree }}">
          <a href="#">
            <i class="fa fa-user-o"></i> <span>{{ trans('sidebar.mange_customer') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: {{ $customer }} ">
            @can('customer-list')
              <li class="{{ (request()->is('admin/customers*')) ? 'active' : '' }}">
                <a href="{{route('customers.index')}}">
                  <i class="fa fa-users"></i> <span>{{trans('sidebar.customers')}}</span>
                  <span class="pull-right-container">
                  </span>
                </a>
              </li>
            @endcan
            @can('customer_message-list')
              <li class="{{ (request()->is('admin/c_message*')) ? 'active' : '' }}">
                <a href="{{route('c_message.index')}}">
                 <i class="fa fa-envira"></i> <span>{{ trans('sidebar.customer_message') }}</span>
                </a>
              </li>
            @endcan
          </ul>
        </li>
        @endcan
        
        <li class="treeview {{ $master_tree }}">
          <a href="#">
            <i class="fa fa-copy"></i> <span>{{ trans('sidebar.master_panel') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: '{{ $master }}' ">
              @can('category-list')
              <li class="{{ (request()->is('admin/category*')) ? 'active' : '' }}">
                <a href="{{route('category.index')}}">
                 <i class="fa fa-list"></i> <span>{{ trans('sidebar.categories') }}</span>
                </a>
              </li>
              @endcan
              <li class="{{ (request()->is('admin/country*')) ? 'active' : '' }}">
                <a href="{{route('country.index')}}">
                 <i class="fa fa-copy"></i> <span>{{ trans('sidebar.country') }}</span>
                </a>
              </li>
              <!-- <li class="{{ (request()->is('admin/states*')) ? 'active' : '' }}">
                <a href="{{route('states.index')}}">
                 <i class="fa fa-user-circle"></i> <span>{{ trans('sidebar.state') }}</span>
                </a>
              </li>
              <li class="{{ (request()->is('admin/cities*')) ? 'active' : '' }}">
                <a href="{{route('cities.index')}}">
                 <i class="fa fa-user-circle"></i> <span>{{ trans('sidebar.city') }}</span>
                </a>
              </li> -->
          </ul>
        </li>  
        @can('cms-list')
        <li class="treeview  {{ $cms_tree }}">
          <a href="#">
            <i class="fa fa-list"></i> <span>{{ trans('sidebar.cms') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: {{ $cms }} ">
            <li class="{{ (request()->is('admin/cms*')) ? 'active' : '' }}">
             <a href="{{route('cms.index')}}">
              <i class="fa fa-question-circle"></i> <span>{{ trans('sidebar.cms') }}</span>
             </a>
            </li>
          </ul>
        </li>
        @endcan
        @can('review-list')
        <li class="treeview  {{ $review_tree }}">
          <a href="#">
            <i class="fa fa-star"></i> <span>{{ trans('sidebar.review') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          @can('partner-review-list')
          <ul class="treeview-menu" style="display: {{ $review }} ">
            <li class="{{ (request()->is('admin/partner_review*')) ? 'active' : '' }}">
             <a href="{{route('partner_review.index')}}">
              <i class="fa fa-code-fork"></i> <span>{{ trans('sidebar.partner_review') }}</span>
             </a>
            </li>
          </ul>
          @endcan
        </li>
        @endcan
        @can('refund-list')
        <li class="{{ (request()->is('admin/refund*')) ? 'active' : '' }}">
          <a href="{{route('refund.index')}}">
            <i class="fa fa-undo"></i> <span>{{ trans('sidebar.refund') }}</span>
          </a>
        </li>
        @endcan         
        @can('ticket-list')
        <li class="{{ (request()->is('admin/contact_us*')) ? 'active' : '' }}">
         <a href="{{route('contact_us.index')}}">
          <i class="fa fa-commenting"></i> <span>{{ trans('sidebar.ticket_management') }}</span>
         </a>
        </li>
        @endcan
        @can('reason-list')
        <li class="treeview {{ $cancel_reason_tree }}">
          <a href="#">
            <i class="fa fa-list"></i> <span>{{ trans('sidebar.reason') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: {{$cancel_reasons}} ">
            <li class="{{ (request()->is('admin/reasons*')) ? 'active' : '' }}">
              <a href="{{route('reasons.index')}}">
                <i class="fa fa-question-circle"></i> <span>{{ trans('sidebar.reasons') }}</span>
              </a>
            </li>
            <li class="{{ (request()->is('admin/cancel_reasons*')) ? 'active' : '' }}">
              <a href="{{route('cancel_reasons.index')}}">
                <i class="fa fa-question-circle"></i> <span>{{ trans('sidebar.cancel_reasons') }}</span>
              </a>
            </li>
          </ul>
        </li>
        @endcan
        @can('slider-list')
          <!-- <li class="{{ (request()->is('admin/sliders*')) ? 'active' : '' }}">
           <a href="{{route('sliders.index')}}">
            <i class="fa fa-sliders"></i> <span>{{ trans('sidebar.sliders') }}</span>
           </a>
          </li>  -->
        @endcan 
        @can('featured_salon')
        <li class="{{ (request()->is('admin/featured_salon*')) ? 'active' : '' }}">
          <a href="{{route('featured_salon.index')}}">
            <i class="fa fa-building-o"></i> <span>{{ trans('sidebar.featured_salon') }}</span>
          </a>
        </li>
        @endcan 
        @can('featured_artists')
        <li class="{{ (request()->is('admin/featured_artists*')) ? 'active' : '' }}">
          <a href="{{route('featured_artists.index')}}">
            <i class="fa fa-users"></i> <span>{{ trans('sidebar.featured_artists') }}</span>
          </a>
        </li>
        @endcan  
        @can('faqs-list')
        <li class="{{ (request()->is('admin/faqs*')) ? 'active' : '' }}">
          <a href="{{route('faqs.index')}}">
            <i class="fa fa-question-circle"></i> <span>{{ trans('sidebar.faqs') }}</span>
          </a>
        </li>
        @endcan
        @can('enquiries')  
        <li class="{{ (request()->is('admin/enquiries*')) ? 'active' : '' }}">
          <a href="{{route('enquiries.index')}}">
            <i class="fa fa-question"></i> <span>{{trans('sidebar.enquiries')}}</span>
          </a>
        </li> 
        @endcan
        @can('advertisement-list')
        <li class="{{ (request()->is('admin/advertisements*')) ? 'active' :'' }}">
          <a href="{{route('advertisements.index')}}">
            <i class="fa fa-cog"></i> <span>{{ trans('sidebar.advertisements') }}</span>
          </a>
        </li>
        @endcan
        @can('appointments-list')  
        <li class="{{ (request()->is('admin/appointments*')) ? 'active' : '' }}">
          <a href="{{route('appointments')}}">
            <i class="fa fa-calendar"></i> <span>{{ trans('sidebar.appointments') }}</span>
          </a>
        </li> 
        @endcan
        @can('setting-list')  
        <li class="{{ (request()->is('admin/setting*')) ? 'active' : '' }}">
          <a href="{{route('setting.index')}}">
            <i class="fa fa-cog"></i> <span>{{ trans('sidebar.setting') }}</span>
          </a>
        </li> 
        @endcan
        @can('admin_payout')  
        <li class="treeview {{ $payout_tree }}">
          <a href="#">
            <i class="fa fa-list"></i> <span>{{ trans('sidebar.payout_details') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: {{$payout}} ">
            <li class="{{ (request()->is('admin/admin_payout*') || request()->is('admin/vendor_payouts*')) ? 'active' : '' }}">
              <a href="{{route('admin_payout')}}">
                <i class="fa fa-money"></i> <span>{{ trans('sidebar.admin_payout') }}</span>
              </a>
            </li>
            <li class="{{ (request()->is('admin/admin_profit*')) ? 'active' : '' }}">
              <a href="{{route('admin_profit')}}">
                <i class="fa fa-money"></i> <span>{{ trans('sidebar.admin_profit') }}</span>
              </a>
            </li>
          </ul>
        </li>
        <!-- <li class="{{ (request()->is('admin/admin_payout*') || request()->is('admin/vendor_payout*')) ? 'active' : '' }}">
          <a href="{{route('admin_payout')}}">
            <i class="fa fa-money"></i> <span>{{ trans('sidebar.admin_payout') }}</span>
          </a>
        </li>  -->
        @endcan
        @can('tier_package-list')  
        <li class="{{ (request()->is('admin/tier_packages*')) ? 'active' : '' }}">
          <a href="{{route('tier_packages.index')}}">
            <i class="fa fa-object-group"></i> <span>{{ trans('sidebar.tier_packages') }}</span>
          </a>
        </li> 
        @endcan
        @can('tier_config-list')  
        <li class="{{ (request()->is('admin/tier_config*')) ? 'active' : '' }}">
          <a href="{{route('tier_config.index')}}">
            <i class="fa fa-cogs"></i> <span>{{ trans('sidebar.tier_config') }}</span>
          </a>
        </li> 
        @endcan
        @can('notification-list')
        <li class="{{ (request()->is('admin/notifications*')) ? 'active' : '' }}">
          <a href="{{route('notifications.index')}}">
           <i class="fa fa-bell"></i> <span>{{ trans('sidebar.notification') }}</span>
          </a>
        </li>
        @endcan
        @can('banner_master-list')
        <li class="{{ (request()->is('admin/banner_master*')) ? 'active' : '' }}">
          <a href="{{route('banner_master.index')}}">
           <i class="fa fa-bell"></i> <span>{{ trans('sidebar.banner_master') }}</span>
          </a>
        </li>
        @endcan
      </ul>
    @elseif( auth()->user()->user_type == 'vendor'  || auth()->user()->user_type == 'individual' || auth()->user()->user_type == 'employee')
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">{{ trans('sidebar.main_navigation') }}</li>
        <!-- <li class="active treeview menu-open"> -->
        <li class="@if($routename == 'home') active @endif">
          <a href="{{route('home')}}">
            <i class="fa fa-dashboard"></i> <span>{{ trans('sidebar.dashboard') }}</span>
            <span class="pull-right-container">
              <!-- <i class="fa fa-angle-left pull-right"></i> -->
            </span>
          </a>
        </li>
        @can('business_profile')
        <li class="treeview {{ $profile_tree }}">
          <a href="#">
            <i class="fa fa-copy"></i> <span>{{ trans('sidebar.profile') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: {{ $profile }} ">
          
            <li class="{{ (request()->is('admin/business_profile*')) ? 'active' : '' }}">
              <a href="{{route('business_profile')}}">
                <i class="fa fa-cog"></i> <span>{{ trans('sidebar.business_info') }}</span>
              </a>
            </li>

            <li class="{{ (request()->is('admin/certificate*')) ? 'active' : '' }}">
              <a href="{{route('certificates')}}">
                <i class="fa fa-cog"></i> <span>{{ trans('sidebar.certificate') }}</span>
              </a>
            </li>
       
            @can('working_hours')
              <li class="{{ (request()->is('admin/profile/working_hours*')) ? 'active' : '' }}">
                <a href="{{route('working_hours')}}">
                  <i class="fa fa-cog"></i> <span>{{ trans('sidebar.working_hours') }}</span>
                </a>
              </li>
            @endcan
            @can('business_profile')
              <li class="{{ (request()->is('admin/bank_details*')) ? 'active' : '' }}">
                <a href="{{route('bank_detail')}}">
                  <i class="fa fa-cog"></i> <span>{{ trans('sidebar.bank_detail') }}</span>
                </a>
              </li> 
            @endcan
          </ul>
        </li>
        @endcan
        @can('employee_profile')
          <!-- <li class="{{ (request()->is('admin/employee_profile*')) ? 'active' : '' }}">
            <a href="{{route('employee_profile')}}">
              <i class="fa fa-cog"></i> <span>{{ trans('sidebar.employee_detail') }}</span>
            </a>
          </li> -->
        @endcan

        @if(auth()->user()->user_type != 'employee')
          <li class="treeview {{ $vndr_master_tree }}">
          <a href="#">
            <i class="fa fa-copy"></i> <span>{{ trans('sidebar.master_panel') }}</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu" style="display: {{ $vndr_master }} ">
              @can('category-list')
                <li class="{{ (request()->is('admin/category*')) ? 'active' : '' }}">
                  <a href="{{route('category.index')}}">
                   <i class="fa fa-list"></i> <span>{{ trans('sidebar.categories') }}</span>
                  </a>
                </li>
              @endcan
              @can('service-list')
                <li class="{{ (request()->is('admin/services*')) ? 'active' : '' }}">
                  <a href="{{route('services.index')}}">
                    <i class="fa fa-cog"></i> <span>{{ trans('sidebar.services') }}</span>
                  </a>
                </li>
              @endcan
              @can('product-list')
                <li class="{{ (request()->is('admin/products*')) ? 'active' : '' }}">
                  <a href="{{route('products.index')}}">
                    <i class="fa fa-product-hunt"></i> <span>{{ trans('sidebar.products') }}</span>
                  </a>
                </li>
              @endcan
          </ul>
        </li> 
        @endif 
          @can('employee-list')
            <li class="{{ (request()->is('admin/employees*')) ? 'active' : '' }}">
              <a href="{{route('employees.index')}}">
                <i class="fa fa-users"></i> <span>{{ trans('sidebar.employees') }}</span>
              </a>
            </li> 
          @endcan
          @can('offer-list')
            <li class="treeview {{$offer_tree}}">
              <a href="#">
                <i class="fa fa-gift"></i> <span>{{ trans('sidebar.manage_offers') }}</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
              </a>
              <ul class="treeview-menu" style="display: {{ $offer }} ">
                @can('offer-list')
                  <li class="{{ (request()->is('admin/offers*')) ? 'active' : '' }}">
                    <a href="{{route('offers.index')}}">
                      <i class="fa fa-gift"></i> <span>{{ trans('sidebar.offers') }}</span>
                    </a>
                  </li>
                @endcan
                @can('coupon_offer-list')
                  <li class="{{ (request()->is('admin/coupon_offers*')) ? 'active' : '' }}">
                    <a href="{{route('coupon_offers.index')}}">
                      <i class="fa fa-gift"></i> <span>{{ trans('sidebar.coupon_offers') }}</span>
                    </a>
                  </li>
                @endcan
              </ul>
            </li>
          @endcan
          @can('ticket-list')
            <li class="{{ (request()->is('admin/tickets*')) ? 'active' : '' }}">
              <a href="{{route('tickets.index')}}">
               <i class="fa fa-commenting"></i> <span>{{ trans('sidebar.ticket_management') }}</span>
              </a>
            </li>
          @endcan
          @can('schedule-appointment')
            <li class="{{ (request()->is('admin/schedule_appointment*')) ? 'active' : '' }}">
              <a href="{{route('schedule_appointment',0)}}">
               <i class="fa fa-calendar"></i> <span>{{ trans('sidebar.schedule_appointment') }}</span>
              </a>
            </li>
          @endcan
          @can('bookings-list')
            <li class="{{ (request()->is('admin/bookings*')) ? 'active' : '' }}">
              <a href="{{route('bookings',0)}}">
               <i class="fa fa-calendar-o"></i> <span>{{ trans('sidebar.bookings') }}</span>
              </a>
            </li>
          @endcan
          @can('payments')
            <li class="{{ (request()->is('admin/vendor_payments*')) ? 'active' : '' }}">
              <a href="{{route('payments')}}">
               <i class="fa fa-money"></i> <span>{{ trans('sidebar.payments') }}</span>
              </a>
            </li>
          @endcan
          @can('notification-list')
            <li class="{{ (request()->is('admin/notifications*')) ? 'active' : '' }}">
              <a href="{{route('notifications.index')}}">
               <i class="fa fa-bell"></i> <span>{{ trans('sidebar.notification') }}</span>
              </a>
            </li>
          @endcan
          @can('tier')
            <li class="{{ (request()->is('admin/tier')) ? 'active' : '' }}">
              <a href="{{route('tier_index')}}">
               <i class="fa fa-calendar-o"></i> <span>{{ trans('sidebar.tier') }}</span>
              </a>
            </li>
          @endcan
          @can('enquiries-create')
            <li class="{{ (request()->is('admin/enquiries/create')) ? 'active' : '' }}">
              <a href="{{route('enquiries.create')}}">
               <i class="fa fa-calendar-o"></i> <span>{{ trans('sidebar.contact_us') }}</span>
              </a>
            </li>
          @endcan
        </li>
      </ul>
      @endif
      @role('developer')
      <ul class="sidebar-menu" data-widget="tree">
        @can('permission-list')
          <li class="{{ (request()->is('admin/permissions*')) ? 'active' : '' }}">
            <a href="{{ route('permissions.index') }}">
              <i class="fa fa-tags"></i> <span>Manage Permissions</span>
            </a>
          </li>
        @endcan
        @can('permission-list')
          <li class="{{ (request()->is('admin/roles*')) ? 'active' : '' }}">
            <a href="{{ route('roles.index') }}">
              <i class="fa fa-briefcase"></i> <span>Manage Roles</span>
            </a>
          </li>
        @endcan  
      </ul>   
      @endrole 
    </section>
  </aside>