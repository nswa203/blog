@extends('main')

@if ($post)
	@section('title',"| $post->slug")

	@section('content')
		<div class="row">
			<div class="col-md8 offset-md-2">
				<h1>{{ $post->title }}</h1>
				<p>{{ $post->body }}</p>
				<hr>
				<p>Posted In: {{ $post->category->name }}</p>
			</div>
		</div>
	@endsection

@endif
