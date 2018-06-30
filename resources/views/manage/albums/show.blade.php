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
				<a href="{{ asset('images/'.$album->image) }}">
					<img src="{{ asset('images/'.$album->image) }}" width="150px" class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
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
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url($album->slug) }}">{{ url($album->slug) }}</a></dd>
						<dt class="col-sm-5">Album ID:</dt>
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
						<div class="col-sm-12 mb-3">
						{!! Html::decode(link_to_route('photos.createMultiple', '<i class="fas fa-image mr-2"></i>Add Photos', [$album->id], ['class'=>'btn btn-outline-primary btn-block'])) !!}
						</div>

						<div class="col-sm-12">
						{!! Html::decode(link_to_route('albums.index', '<i class="fas fa-images mr-2"></i>See All Albums', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>						
					</div>
				</div>
				@if($album->image)
					{{-- <img src="{{ asset('images/'.$album->image) }}" width="100%" class="mt-3"/> --}}
				@endif	
			</div>
		</div>	
		
		@if($album->photos->count() && $photos)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Photos
							<span class="h1-suffix">(This Album has {{ $album->photos->count()==1 ? '1 Photo' : $album->photos->count().' Photos' }} assigned.)</span>
						</h1>
						<table class="table table-hover table-responsive-lg">
							<thead class="thead-dark">
								<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
								<th>Title</th>
								<th>Albums</th>
								<th>Tags</th>
								<th width="120px">Published</th>
								<th width="130px" class="text-right">Page {{$photos->currentPage()}} of {{$photos->lastPage()}}</th>
							</thead>
							<tbody>						
								@foreach($album->photos as $photo)
									<tr>
										<th>{{ $photo->id }}</th>
										<td>
											{{ substr(strip_tags($photo->title), 0, 156) }}{{ strlen(strip_tags($photo->title))>156 ? '...' : '' }}
										</td>
										<td>
											@foreach ($photo->albums as $album)
												<a href="{{ route('albums.show', $album->id) }}"><span class="badge badge-info">{{ substr($album->slug, 0, 16) }}</span></a>
											@endforeach	
										</td>
										<td>
											@foreach ($photo->tags as $tag)
												<a href="{{ route('tags.show', [$tag->id, 'Photos']) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
											@endforeach
										</td>
										<th>
											@if($photo->published_at)
												<span class="text-success">{{ date('j M Y', strtotime($photo->published_at)) }}</span>
											@else	
												<span class="text-danger">{{ $status_list[$album->status] }}</span>
											@endif	
										</th>		
										<td class="text-right" nowrap>
											<a href="{{ route('photos.show', $photo->id) }}" class="btn btn-sm btn-success">
												<span class="far fa-image"></span>
											</a>											
											<a href="{{ route('photos.edit', $photo->id) }}" class="btn btn-sm btn-primary">
												<span class="far fa-edit"></span>
											</a>
											<a href="{{ route('photos.delete', $photo->id) }}" class="btn btn-sm btn-danger">
												<span class="far fa-trash-alt"></span>
											</a>	
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="d-flex justify-content-center">
							{{ $photos->appends(Request::all())->render() }} 
						</div>
					</div>
				</div>
			</div>
		@endif
	@endif
@endsection

@section('scripts')
@endsection
