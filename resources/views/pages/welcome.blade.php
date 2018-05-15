@extends('main')

@section('title','| Homepage')

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<h1 class="display-4">Welcome to {{ env('APP_NAME') }}!</h1>
				<p class="lead">Thank you so much for visiting. This is my test website built with Laravel. Please read my latest post!</p>
				<a class="btn btn-primary btn-lg mt-4" href="#" role="button">Popular Post</a>
			</div>
		</div>
	</div>

	<div class="row mt-5">
		<div class="col-md-8">
			@if($posts)
				@foreach($posts as $post)
					<div class="post">
						<h3>{{ $post->title}}</h3>
						<p>{{ substr($post->body,0,256)}}{{ strlen($post->body)>256?'...':'' }}</p>
						<a href="{{ url($post->slug) }}" class="btn btn-primary">Read More</a>
					</div>
					<hr>
				@endforeach
			@endif
		</div>

		<div class="col-md-3 offset-md-1">
			<h2>Sidebar</h2>
			<p>Just some text to show the extent of the sidebar.</p>
		</div>
	</div>
@endsection
