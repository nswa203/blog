@extends('main')

@section('title','| Homepage')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-12 myWrap">
			<div class="jumbotron">
				<div class="row">
					<div class="col-md-2 inline">
						<img style="margin-top:-32px; max-width:100%; max-height:128px; float:left;" src="{{ asset('favicon.ico') }}">
					</div>

					<div class="col-md-10 mb-5">
						<h1 class="display-4">Welcome to {{ $data['name'] }}!</h1>
					</div>
				</div>	
				<p class="lead">Thank you so much for visiting. This is my test website built with Laravel. Please read my latest post!</p>
				<a class="btn btn-primary btn-lg mt-4" href="/blog" role="button">Popular Posts</a>
			</div>
		</div>
	</div>	

	<div class="row mt-5">
		<div class="col-md-8 myWrap">
			@if($posts)
				@foreach($posts as $post)
					<div class="post">
						<h3>{{ $post->title}}</h3>
						<p>{{ myTrim($post->body, 256) }}</p>
						<a href="{{ url($post->slug) }}" class="btn btn-primary">Read More</a>
					</div>
					@if (!$loop->last)
						<hr>
					@endif	
				@endforeach
			@endif
		</div>

		<div class="col-md-3 offset-md-1 myWrap">
			<h3><span class="fas fa-tag mr-2"></span>Tags</h3>
			<p>Use our Tag Table to find a subject of interest.</p>
			@if (Auth::check())
				<p>
					<a href="{{ url('blog?pus=Y') }}">
					<span class="badge badge-dark">{{ Auth::user()->name }}</span></a>
				</p>
			@endif			
			<p>
				@foreach ($categories as $category)
					<a href="{{ url('blog?pca='.$category->name) }}""><span class="badge badge-secondary">{{ $category->name }}</span></a>
				@endforeach 				
			</p>
			<p>
				@foreach ($tags as $tag)
					<a href="{{ url('blog?pta='.$tag->name) }}""><span class="badge badge-info">{{ $tag->name }}</span></a>
				@endforeach 				
			</p>
		</div>


	</div>
@endsection

@section('scripts')
@endsection
