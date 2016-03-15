@extends('templates.main')

@section('title')
it's works.
@endsection

@section('contents')
<ul>
@for($i=0;$i < 20;$i++)
    <li>{{{ $i }}}</li>
@endfor
</ul>
<h1>Test Header</h1>
@endsection
