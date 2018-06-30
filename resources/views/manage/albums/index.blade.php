@extends('manage')

@section('title','| Manage Albums')

@section('stylesheets')
@endsection

@section('content')
	@if($albums)
		<div class="row">
			<div class="col-md-9">
				<h1><a id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-images mr-4"></span>
					@endif 				 Manage Albums
				</a></h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('albums.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Create New Album</a>
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
						<th>Slug</th>
						<th>Category</th>
						<th>Author</th>
						<th>#P</th>						
						<th width="120px">Published</th>
						<th width="130px">Page {{$albums->currentPage()}} of {{$albums->lastPage()}}</th>
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
									<a href="{{ route('albums.show', $album->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('albums.edit', $album->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $albums->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
