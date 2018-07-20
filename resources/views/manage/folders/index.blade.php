@extends('manage')

@section('title','| Manage Folders')

@section('stylesheets')
@endsection

@section('content')
	@if($folders)
		<div class="row">
			<div class="col-md-9">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-folder mr-4"></span>
					@endif 				 Manage Folders
				</a></h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('folders.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Create New Folder</a>
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
						<th>Name</th>
						<th>Slug</th>
						<th>Category</th>
						<th>Owner</th>
						<th>Size</th>
						<th>Used</th>
						<th>Status</th>
						<th width="120px">Updated</th>
						<th width="130px">Page {{$folders->currentPage()}} of {{$folders->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($folders as $folder)
							<tr>
								<th>{{ $folder->id }}</th>
								<td>{{ $folder->name }}</td>
								<td><a href="{{ url('f/'.$folder->slug) }}">{{ $folder->slug }}</a></td>
								<td>
									<a href="{{ route('categories.show', [$folder->category_id, session('zone')]) }}"><span class="badge badge-info">{{ $folder->category->name }}</span></a>
								</td>
								<td>
									<a href="{{ route('users.show', $folder->user_id) }}">{{ $folder->user->name }}</a>
								</td>
								<td>{{ ($folder->max_size/ 1000000) }}M</td>
								<td class="{{ $folder->size / $folder->max_size > .85 ? 'text-danger' : 'text-success' }}">
									{!! round(($folder->size / $folder->max_size) * 100, 2) !!}%
								</td>
								<td>{{ $status_list[$folder->status] }}</td>
								<td>{{ date('j M Y',strtotime($folder->updated_at)) }}</td>
								<td class="text-right" nowrap>
									<a href="{{ route('folders.show', $folder->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('folders.edit', $folder->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $folders->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
