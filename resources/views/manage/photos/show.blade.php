@extends('manage')

@section('title','| Manage View Photo')

@section('stylesheets')
@endsection

@section('content')
	@if($photo)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-image mr-4"></span>{{ $photo->title }}</a></h1>
				<hr>
				<a href="{{ asset('images/'.$photo->image) }}">
					<img src="{{ asset('images/'.$photo->image) }}" width="150px" class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
				<p class="lead">{!! $photo->description !!}</p>
				<hr>
				<div class="tags">
					@foreach ($photo->tags as $tag)
						<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">Albums:</dt>
						<dd class="col-sm-7">
							@foreach ($photo->albums as $album)
								<a href="{{ route('albums.show', $album->id) }}"><span class="badge badge-info">{{ $album->slug }}</span></a>
							@endforeach	
						</dd>
						<dt class="col-sm-5">Photo ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('photos.show', $photo->id) }}">{{ $photo->id }}</a></dd>
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
							@if($photo->published_at)
								{{ date('j M Y, h:i a', strtotime($photo->published_at)) }}
							@else	
								<span class="text-danger">{{ $photo->status_name }}</span>
							@endif	
						</dd>
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($photo->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($photo->updated_at)) }}</dd>
					</dl>

					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('photos.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$photo->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['photos.delete', $photo->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('photos.index', '<i class="fas fa-images mr-2"></i>See All Photos', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>

				</div>
				@if($photo->image)
					{{-- <img src="{{ asset('images/'.$photo->image) }}" width="100%" class="mt-3"/> --}}
				@endif	
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
