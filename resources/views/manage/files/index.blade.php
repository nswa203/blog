@extends('manage')

@section('title','| Manage Files')

@section('stylesheets')
@endsection

@section('content')
	@if($files)
		<div class="row">
			<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip" data-placement="top" title="Toggle NavBar">
					@if (isset($search)) <span class="fas fa-search mr-4"></span>
					@else 				 <span class="fas fa-folder-open mr-4"></span>
					@endif 				 Manage Files
				</a></h1>
			</div>

			<div class="col-md-3">
				<a href="{{ route('files.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><i class="fas fa-plus-circle mr-2 mb-1"></i>Add Files</a>
			</div>
			<div class="col-md-12">
				<hr>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-12 myWrap">
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Title</th>
						<th>Folder</th>
						<th>Tags</th>
						<th>Owner</th>
						<th>Size</th>
						<th width="120px">Published</th>
						<th width="130px" class="text-right">Page {{$files->currentPage()}} of {{$files->lastPage()}}</th>
					</thead>
					<tbody>
						@foreach($files as $file)
							<tr>
								<th>{{ $file->id }}</th>
								<td>{{ myTrim($file->title, 32) }}</td>
								<td>
									<a href="{{ route('folders.show', [$file->folder->id, session('zone')]) }}">
										<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
											{{ myTrim($file->folder->name, 32) }}
										</span>
									</a>
							    </td>
								<td>
									@foreach ($file->tags as $tag)
										<a href="{{ route('tags.show', [$tag->id, session('zone')]) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
									@endforeach
								</td>
								<td>
									<a href="{{ route('users.show', $file->folder->user_id) }}">{{ $file->folder->user->name }}</a>
								</td>
								<td>{{ mySize($file->size) }} </td>
								<th>
									@if($file->published_at)
										<span class="{{ $file->folder->status==1?'text-success':'text-danger' }}">
											{{ date('j M Y', strtotime($file->published_at)) }}, {{ $list['d'][$file->folder->status] }}
										</span>
									@else	
										<span class="text-danger">{{ $list['f'][$file->status] }}, {{ $list['d'][$file->folder->status] }}</span>
									@endif	
								</th>
								<td class="text-right" nowrap>
									<a href="{{ route('files.show', $file->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('files.edit', $file->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $files->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif
@endsection

@section('scripts')
@endsection
