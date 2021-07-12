@extends('layouts.admin.app')

@section('content')

<center><img src="{{ asset('/img/unauthorized.jpg')}}" height="200px" width="400px" style="margin-top: 150px;"></center>
<center><h2>{{$message}}</h2></center>

@endsection

