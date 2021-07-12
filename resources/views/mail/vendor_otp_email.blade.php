@component('mail::message')
<h2>{{ trans('notifications.customer_email_verification_greeting', ['user' => $user->first_name]) }} </h2>
<br/>

{{ trans('notifications.customer_email_verification_message') }}
<br/>

<h2 style="align-items: center;">{{$otp}}</h2>
With Regards, 
{{ $app_settings['app_name'] }} Team
@endcomponent

