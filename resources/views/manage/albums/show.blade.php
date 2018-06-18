@extends('manage')

@section('title','| Manage View Album')

@section('stylesheets')
@endsection

@section('content')
	@if($album)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-images mr-4"></span>Album {{ $album->title }}</a></h1>
				<hr>

				<p class="lead">{!! $album->description !!}</p>
				<hr>
				<div class="tags">
					@foreach ($album->tags as $tag)
						<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url($album->slug) }}">{{ url($album->slug) }}</a></dd>
						<dt class="col-sm-5">Post ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('albums.show', $album->id) }}">{{ $album->id }}</a></dd>
						<dt class="col-sm-5">Category:</dt>						
						<dd class="col-sm-7">
							<a href="{{ route('categories.show', [$album->category->id, session('zone')]) }}"><span class="badge badge-info">{{ $album->category->name }}</span></a>
						</dd>
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
							@if($album->published_at)
								{{ date('j M Y, h:i a', strtotime($album->published_at)) }}
							@else	
								<span class="text-danger">{{ $album->status_name }}</span>
							@endif	
						</dd>							
						<dt class="col-sm-5">Author:</dt>
						<dd class="col-sm-7">
							@if($album->user->id)
								<a href="{{ route('users.show', $album->user->id) }}">{{ $album->user->name }}</a>
							@endif	
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($album->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($album->updated_at)) }}</dd>
					</dl>

					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('albums.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$album->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['albums.delete', $album->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('albums.index', '<i class="fas fa-images mr-2"></i>See All Albums', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
				@if($album->image)
					<img src="{{ asset('images/'.$album->image) }}" width="100%" class="mt-3"/>
				@endif	
			</div>
		</div>	
		
		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card card-body bg-light">
				<h1>
					Photos
					<span class="h1-suffix">(This Album has {{ $album->photo->count()==1 ? '1 Photo' : $album->photo->count().' Photos' }} assigned.)</span>
				</h1>
					<table class="table table-hover">
						<thead class="thead-dark">
							<th>#</th>
							<th>OK</th>
							<th>Name</th>
							<th>eMail</th>
							<th>Comment</th>
							<th width="120px">Created At</th>
							<th width="96px"></th>
						</thead>
						<tbody>						
							@foreach($album->photo as $photo)
								<tr>
									<th>{{ $photo->id }}</th>
									<td>
										{!! $photo->approved ? "<span class='fas fa-check text-success'></span>" : "<span class='fas fa-times text-danger'></span>" !!}
									</td>
									<td>{{ $photo->name }}</td>
									<td>{{ $photo->email }}</td>
									<td>{{ substr(strip_tags($photo->photo), 0, 256)}}{{ strlen(strip_tags($photo->photo))>256 ? '...' : '' }}</td>
									<td>{{ date('j M Y', strtotime($photo->created_at)) }}</td>

									<td>
										<a href="{{ route('photo.edit', $photo->id) }}" class="btn btn-sm btn-primary">
											<span class="far fa-edit"></span>
										</a>
										<a href="{{ route('photo.delete', $photo->id) }}" class="btn btn-sm btn-danger">
											<span class="far fa-trash-alt"></span>
										</a>	
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						
					</div>
				</div>
			</div>
		</div>
		
	@endif
@endsection

@section('scripts')
@endsection
