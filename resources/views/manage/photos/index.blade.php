@extends('manage')

@section('title','| Manage Photos')

@section('stylesheets')
@endsection

@section('content')
	@if($photos)
		<div class="row">
			<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-image mr-4"></span>
					@endif 				 Manage Photos
				</a></h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('photos.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Add Photos</a>
			</div>
			<div class="col-md-12">
				<hr>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Title</th>
						<th>Albums</th>
						<th>Tags</th>
						<th width="120px">Published</th>
						<th width="130px">Page {{$photos->currentPage()}} of {{$photos->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($photos as $photo)
							<tr>
								<th>{{ $photo->id }}</th>
								<td>{{ myTrim($photo->title, 48) }}</td>
								<td>
									@foreach ($photo->albums as $album)
										<a href="{{ route('albums.show', $album->id) }}"><span class="badge badge-info">{{ substr($album->slug, 0, 16) }}</span></a>
									@endforeach	
								</td>
								<td>
									@foreach ($photo->tags as $tag)
										<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
									@endforeach
								</td>
								<th>
									@if($photo->published_at)
										<span class="text-success">{{ date('j M Y', strtotime($photo->published_at)) }}</span>
									@else	
										<span class="text-danger">{{ $status_list[$photo->status] }}</span>
									@endif	
								</th>
								<td class="text-right" nowrap>
									<a href="{{ route('photos.show', $photo->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('photos.edit', $photo->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $photos->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
