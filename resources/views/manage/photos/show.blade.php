@extends('manage')

@section('title','| Manage View Photo')

@section('stylesheets')
@endsection

@section('content')
	@if($photo)
		<div class="row">
			<div class="col-md-8 myWrap">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-image mr-4"></span>{{ $photo->title }}</a></h1>
				<hr>
				<a href="{{ route('photos.showImage', $photo->id) }}">
					<img src="{{ asset('images/'.$photo->image) }}"
						class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
				<p class="lead">{!! $photo->description !!}</p>
				<hr>
				<div class="row">
					<div class="col-md-3">
						@foreach ($photo->tags as $tag)
							<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
						@endforeach
					</div>
					<div class="col-md-9">
						<button class="btn btn-outline-info float-right btn-sm ml-3" type="button"
					  		data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
					    	Details
						</button>
						<div class="collapse" id="collapseExample">
						  <div class="card card-body">
							{!! $exif['meta'] !!}
						  </div>
						</div>
					</div>
				</div>

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
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
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

		@include('partials.__albums', ['count' => $photo->albums->count(), 'zone' => 'Photo', 'page' => 'pageA'])
		@include('partials.__tags',   ['count' => $photo->tags->count(),   'zone' => 'Photo', 'page' => 'pageT'])

		@if($photo->albums->count() && $albums)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Albums
							<span class="h1-suffix">(This Photo has {{ $photo->albums->count()==1 ? '1 Album' : $photo->albums->count().' Albums' }} assigned.)</span>
						</h1>
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>Title</th>
									<th>Slug</th>
									<th>Category</th>
									<th>Author</th>
									<th>#P</th>						
									<th width="120px">Published</th>
									<th width="130px" class="text-right">Page {{$albums->currentPage()}} of {{$albums->lastPage()}}</th>
								</thead>
								<tbody>
									@foreach($albums as $album)
										<tr>
											<th>{{ $album->id }}</th>
											<td>{{ $album->title }}</td>
											<td><a href="{{ url($album->slug) }}">{{ $album->slug }}</a></td>
											<td>
												<a href="{{ route('categories.show', [$album->category_id, session('zone')]) }}"><span class="badge badge-info">{{ $album->category->name }}</span></a>
											</td>
											<td>
												@if($album->user->id)
													<a href="{{ route('users.show', $album->user->id) }}">{{ $album->user->name }}</a>
												@endif	
											</td>
											<td>{{ $album->photos->count() }}</td>
											<th>
												@if($album->published_at)
													<span class="text-success">{{ date('j M Y', strtotime($album->published_at)) }}</span>
												@else	
													<span class="text-danger">{{ $status_list[$album->status] }}</span>
												@endif	
											</th>
											<td class="text-right" nowrap>
												<a href="{{ route('albums.show', $album->id)}}" class="btn btn-sm btn-outline-dark">View Album</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						<div class="d-flex justify-content-center">
							{{ $albums->appends(Request::all())->render() }} 
						</div>
					</div>
				</div>
			</div>
		@endif

	@endif
@endsection

@section('scripts')
@endsection
