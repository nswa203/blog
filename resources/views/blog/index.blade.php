@extends('main')

@section('title','| Blog')

@section('content')
	<div class="row">
		<div class="col-md8 offset-md-2">
			<h1><span class="fas fa-list-alt mr-4"></span>Blog</h1>
		</div>
	</div>

	@foreach ($posts as $post)
		<div class="row mt-5">
			<div class="col-md8 offset-md-2">
				<h2>{{ $post->title }}</h2>
				<h5>Published: {{ date('j M Y, h:ia',strtotime($post->created_at)) }}</h5>
				<p>{{ substr(strip_tags($post->body),0,256) }}{{ strlen(strip_tags($post->body))>256?'...':'' }}</p>
				<a href="{{ route('blog.single', $post->slug) }}" class="btn btn-primary">Read More</a>
				<hr>
			</div>
		</div>
	@endforeach

	<div class="row">
		<div class="col-md-12">
			<div class="d-flex justify-content-center">
				{!! $posts->render() !!}
			</div>
		</div>
	</div>
@endsection