@extends('manage')

@section('title','| Manage Delete Post')

@section('stylesheets')
@endsection

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE POST {{ $post->slug }}</a></h1>
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
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url($post->slug) }}">{{ url($post->slug) }}</a></dd>
						<dt class="col-sm-5">Post ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('posts.show', $post->id) }}">{{ $post->id }}</a></dd>
						<dt class="col-sm-5">Category:</dt>						
						<dd class="col-sm-7">
							<a href="{{ route('categories.show', [$post->category->id, session('zone')]) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
						</dd>
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
							@if($post->published_at)
								{{ date('j M Y, h:i a', strtotime($post->published_at)) }}
							@else	
								<span class="text-danger">{{ $status_list[$post->status] }}</span>
							@endif	
						</dd>							
						<dt class="col-sm-5">Author:</dt>
						<dd class="col-sm-7">
							@if($post->user->id)
								<a href="{{ route('users.show', $post->user->id) }}">{{ $post->user->name }}</a>
							@endif
						</dd>													
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
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
