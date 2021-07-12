@component('mail::message')
<h2>{{ trans('notifications.contact_us_greetings') }} </h2>
<br/>

{{ trans('notifications.contact_us_message') }} <br/>
<b>{{ trans('notifications.customer_name') }} : </b> {{$data['full_name']}} <br/>
<b>{{ trans('notifications.customer_email') }} : </b> {{$data['email'] ?? ''}} <br/>
<b>{{ trans('notifications.customer_phone') }} : </b> {{$data['mobile_number']}} <br/>
<b>{{ trans('notifications.message') }} : </b> {{$data['message']}}
<br/>

{{ trans('notifications.thanks') }},<br>
{{ $app_settings['app_name'] }} Team
@endcomponent
