@component('mail::message')
<h2>{{trans('notify.dear')}} {{$data['user']}}</h2>
<br/>
 {!! $data['content'] !!} 
<br/><br/>
{{trans('notify.thank_regards')}},<br>
{{trans('notify.support_team')}}<br>
{{trans('notify.email_address')}} : {{App\Models\Setting::get('contact_email')}}

@endcomponent

@php
//die;
@endphp