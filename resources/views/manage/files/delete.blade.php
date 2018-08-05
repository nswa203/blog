@extends('manage')

@section('title','| Manage Delete Photo')

@section('stylesheets')
@endsection

@section('content')
	@if($photo)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE PHOTO {{ $photo->slug }}</a></h1>
				<hr>

				<h3>Title:</h3>
				<p class="lead">{!! $photo->title !!}</p>
				
				<h3>Description:</h3>
				<p class="lead">{!! $photo->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">Albums:</dt>
						<dd class="col-sm-7">
							@foreach ($photo->albums as $album)
								<a href="{{ route('albums.show', $album->id) }}"><span class="badge badge-info">{{ $album->slug }}</span></a>
							@endforeach	
						</dd>
						<dt class="col-sm-5">Photo ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('photos.show', $photo->id) }}">{{ $photo->id }}</a></dd>
						<dt class="col-sm-5">Snapped:</dt>						
						<dd class="col-sm-7">
							@if($photo->taken_at)
								{{ date('j M Y, h:i a', strtotime($photo->taken_at)) }}
							@endif
						</dd>
						<dt class="col-sm-5">Published:</dt>						<dd class="col-sm-7">
							@if($photo->published_at)
								{{ date('j M Y, h:i a', strtotime($photo->published_at)) }}
							@else	
								<span class="text-danger">{{ $status_list[$photo->status] }}</span>
							@endif	
						</dd>
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($photo->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($photo->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-12">
							{!! Form::open(['route'=>['photos.destroy', $photo->id], 'method'=>'DELETE']) !!}
								{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
								{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block mt-3"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
							{!! Form::close() !!}
						</div>
					</div>
				</div>
				@if($photo->image)
					<img class="mt-4" src="{{ asset('images/'.$photo->image) }}" width="100%" />
				@endif
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
