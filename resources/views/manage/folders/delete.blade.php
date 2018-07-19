@extends('manage')

@section('title','| Manage Delete Album')

@section('stylesheets')
@endsection

@section('content')
	@if($album)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-trash-alt mr-4"></span>DELETE ALBUM {{ $album->slug }}</a></h1>
				<hr>

				<h3>Title:</h3>
				<p class="lead">{!! $album->title !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $album->slug !!}</p>

				<h3>Description:</h3>
				<p class="lead">{!! $album->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url($album->slug) }}">{{ url($album->slug) }}</a></dd>
						<dt class="col-sm-5">Album ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('albums.show', $album->id) }}">{{ $album->id }}</a></dd>
						<dt class="col-sm-5">Category:</dt>						
						<dd class="col-sm-7">
							<a href="{{ route('categories.show', $album->category->id) }}"><span class="badge badge-info">{{ $album->category->name }}</span></a>
						</dd>
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
							@if($album->published_at)
								{{ date('j M Y, h:i a', strtotime($album->published_at)) }}
							@else	
								<span class="text-danger">{{ $status_list[$album->status] }}</span>
							@endif	
						</dd>							
						<dt class="col-sm-5">Author:</dt>
						<dd class="col-sm-7">
							@if($album->user->id)
								<a href="{{ route('users.show', $album->user->id) }}">{{ $album->user->name }}</a>
							@endif
						</dd>												
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($album->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($album->updated_at)) }}</dd>
					</dl>
					
					<hr class="hr-spacing-top">
					{!! Form::open(['route'=>['albums.destroy', $album->id], 'method'=>'DELETE']) !!}
					<dt>
						@if($album->photos->count()>0)
						<label for="delete-photos">
							{{ Form::checkbox('delete_photos', '1', null, ['class'=>'font-bold', 'hidden'=>'', 'id'=>'delete-photos']) }}
							<span class="span text-danger">
								Also DELETE THE
								{{ $album->photos->count()==1 ? '1 Photo' : $album->photos->count().' Photos' }}
								in this album
							</span>
						</label>
						@else
							<span class="span text-success"> This Album contains NO photos</span>
						@endif
					</dt>

					<hr class="hr-spacing-top mt-2">
					<div class="row">
						<div class="col-sm-12">
								{{ 	Form::button('<i class="fas fa-trash-alt mr-2"></i>YES DELETE NOW', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
								{!! Html::decode('<a href='.url()->previous().' class="btn btn-outline-danger btn-block mt-3"><span class="fas fa-times-circle mr-2"></span>Cancel</a>') !!}
						</div>
					</div>
					{!! Form::close() !!}
				</div>
				@if($album->image)
					<img class="mt-4" src="{{ asset('images/'.$album->image) }}" width="100%" />
				@endif
			</div>
		</div>	
	@endif
@endsection

@section('scripts')
@endsection
