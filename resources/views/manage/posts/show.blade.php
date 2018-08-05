@extends('manage')

@section('title','| Manage View Post')

@section('stylesheets')
@endsection

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-file-alt mr-4"></span>{{ $post->title }}</a></h1>
				<hr>
				@if($post->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$post->banner) }}" width="100%" />
					</div>
				@endif

				<p class="lead">{!! $post->body !!}</p>
				<hr>
				<div class="tags">
					@foreach ($post->tags as $tag)
						<a href="{{ route('tags.show', $tag->id) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					
					@include('partials.__postsMeta')

					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('posts.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$post->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['posts.delete', $post->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('posts.index', '<i class="fas fa-file-alt mr-2"></i>See All Posts', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
				@if($post->image)
					<img src="{{ asset('images/'.$post->image) }}" width="100%" class="mt-3"/>
				@endif	
			</div>
		</div>	

		@include('partials.__albums',   ['count' => $post->albums->count(),   'zone' => 'Post', 'page' => 'pageA'])
		@include('partials.__comments', ['count' => $post->comments->count(), 'zone' => 'Post', 'page' => 'pageC'])
		@include('partials.__folders',  ['count' => $post->folders->count(),  'zone' => 'Post', 'page' => 'pageF'])

	@endif
@endsection

@section('scripts')
@endsection
