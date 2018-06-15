@extends('main')

@section('title','| Homepage')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12">
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-2 inline">
						<img style="margin-top:-32px; max-width:100%; max-height:128px; float:left;" src="{{ asset('favicon.ico') }}">
					</div>

					<div class="col-md-10 mb-5">
						<h1 class="display-4">Welcome to {{ env('APP_NAME') }}!</h1>
					</div>
				</div>	
				<p class="lead">Thank you so much for visiting. This is my test website built with Laravel. Please read my latest post!</p>
				<a class="btn btn-primary btn-lg mt-4" href="/blog" role="button">Popular Posts</a>
			</div>
		</div>
	</div>	

	<div class="row mt-5">
		<div class="col-md-8">
			@if($posts)
				@foreach($posts as $post)
					<div class="post">
						<h3>{{ $post->title}}</h3>
						<p>{{ substr(strip_tags($post->body), 0, 256)}}{{ strlen(strip_tags($post->body))>256 ? '...' : '' }}</p>
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

@section('scripts')
@endsection
