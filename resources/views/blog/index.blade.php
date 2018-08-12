@extends('main')

@section('title','| Blog')

@section('content')
	<div class="row">
		<div class="col-md-8 offset-md-2 myWrap">
			<h1><a id="menu-toggle2">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>'{{ $search }}'
				@else 				 <span class="fas fa-list-alt mr-4"></span>
				@endif 				 Posts
			</a></h1>			
		</div>
	</div>

	@foreach ($posts as $post)
		<div class="row mt-3">
			<div class="col-md-6 offset-md-2 myWrap">
				<h2>{{ $post->title }}</h2>
				<h5>Published: {{ date('j M Y, h:ia',strtotime($post->published_at)) }}</h5>
				<p class='mt-4'>{{ myTrim($post->body, 256) }}</p>
				<a href="{{ route('blog.singlePost', $post->slug) }}" class="btn btn-primary">Read More</a>
			</div>
			@if($post->image)
				<div class="col-md-2" style="margin-top:-10px;">
					<a href="{{ asset('images/'.$post->image) }}">
						<img src="{{ asset('images/'.$post->image) }}" width="100%" class="img-frame mt-3"
							onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
						/>
					</a>
				</div>
			@endif
			@if (!$loop->last)
				<div class="col-md-8 offset-md-2"><hr></div>
			@endif		
		</div>
	@endforeach

	<div class="row">
		<div class="col-md-12 myWrap">
			<div class="d-flex justify-content-center">
				{{ $posts->appends(Request::all())->render() }}
			</div>
		</div>
	</div>
@endsection
