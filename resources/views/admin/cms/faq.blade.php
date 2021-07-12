@foreach($faq as $f)
<h3>{{$f->question}}</h3>
<p>{{$f->answer}}</p>
<hr/>
@endforeach