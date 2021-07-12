<!-- {{$cms->page_name}}<br> -->
@if($cms->slug == 'abous_us' || $cms->slug == 'help')
<center>
@endif
@php print_r($cms->content) @endphp
@if($cms->slug == 'abous_us' || $cms->slug == 'help')
</center>
@endif