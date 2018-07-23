@extends('manage')

@section('title','| Manage Delete Post')

@section('stylesheets')
@endsection

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE POST {{ $post->slug }}</a></h1>
				<hr>
				@if($post->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$post->banner) }}" width="100%" />
					</div>
				@endif

				<h3>Title:</h3>
				<p class="lead">{!! $post->title !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $post->slug !!}</p>

				<h3>Excerpt:</h3>
				<p class="lead">{!! $post->excerpt !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">

					@include('partials.__postsMeta')

					<div class="row">
						<div class="col-sm-12">
							{!! Form::open(['route'=>['posts.destroy', $post->id], 'method'=>'DELETE']) !!}
								{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
								{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block mt-3"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
							{!! Form::close() !!}
						</div>
					</div>
				</div>
				@if($post->image)
					<img class="mt-3" src="{{ asset('images/'.$post->image) }}" width="100%" />
				@endif
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
