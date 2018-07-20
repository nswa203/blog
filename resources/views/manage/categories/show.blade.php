@extends('manage')

@section('title',"| Manage View Category")

@section('stylesheets')
@endsection

@section('content')
	@if($category)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-list-alt mr-4"></span>View {{ $category->name }} Category</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $category->name !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ route('categories.show', $category->id) }}</a></dd>
						<dt class="col-sm-5">Category ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('categories.show', $category->id) }}">{{ $category->id }}</a></dd>
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($category->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($category->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('categories.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$category->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['categories.delete', $category->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('categories.index', '<i class="fas fa-list-alt mr-2"></i>See All Categories', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		@if($category->albums->count() && $albums)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Albums
							<span class="h1-suffix">(This Category has {{ $category->albums->count()==1 ? '1 Album' : $category->albums->count().' Albums' }} assigned.)</span>
							<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
							 		 data-toggle="collapse" data-target="#collapsea">
						 	</span></a>							
						</h1>
						<div id="collapsea" class="collapse {{ request()->has('pageA') ? 'show' : 'hide' }}" data-parent="#accordiona">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>Title</th>
									<th>Description</th>
									<th width="120">Updated</th>
									<th width="130" class="text-right">Page {{$albums->currentPage()}} of {{$albums->lastPage()}}</th>
								</thead>
								<tbody>						
									@foreach($albums as $album)
										<tr>
											<th>{{ $album->id }}</th>
											<td>{{ $album->title }}</td>
											<td>
												{{ substr(strip_tags($album->description), 0, 156) }}{{ strlen(strip_tags($album->description)) >156 ? '...' : '' }}
											</td>
											<td>{{ date('j M Y', strtotime($album->updated_at)) }}</td>
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
			</div>
		@endif

		@if($category->folders->count() && $folders)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Folders
							<span class="h1-suffix">(This Category has {{ $category->folders->count()==1 ? '1 Folder' : $category->folders->count().' Folders' }} assigned.)</span>
							<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
							 		 data-toggle="collapse" data-target="#collapsef">
						 	</span></a>
						</h1>
						<div id="collapsef" class="collapse {{ request()->has('pageF') ? 'show' : 'hide' }}" data-parent="#accordionf">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>Name</th>
									<th>Slug</th>
									<th>Category</th>
									<th>Owner</th>
									<th>Used</th>
									<th width="120px">Updated</th>
									<th width="130px" class="text-right">Page {{$folders->currentPage()}} of {{$folders->lastPage()}}</th>
								</thead>
								<tbody>						
									@foreach($folders as $folder)
										<tr>
											<th>{{ $folder->id }}</th>
											<td>{{ $folder->name }}</td>
											<td><a href="{{ url('f/'.$folder->slug) }}">{{ $folder->slug }}</a></td>
											<td>
												<a href="{{ route('categories.show', [$folder->category_id, 'Albums']) }}"><span class="badge badge-info">{{ $folder->category->name }}</span></a>
											</td>
											<td>
												@if($folder->user->id)
													<a href="{{ route('users.show', $folder->user->id) }}">{{ $folder->user->name }}</a>
												@endif	
											</td>
											<td class="{{ $folder->size / $folder->max_size > .85 ? 'text-danger' : 'text-success' }}">
												{{ round(($folder->size / $folder->max_size) * 100, 2) }}%
											</td>
											<td>{{ date('j M Y', strtotime($folder->updated_at)) }}</td>
											<td class="text-right" nowrap>
												<a href="{{ route('folders.show', $folder->id)}}" class="btn btn-sm btn-outline-dark">View Folder</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<div class="d-flex justify-content-center">
								{{ $folders->appends(Request::all())->render() }} 
							</div>
						</div>	
					</div>
				</div>
			</div>
		@endif

		@if($category->posts->count() && $posts)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Posts
							<span class="h1-suffix">(This Category has {{ $category->posts->count()==1 ? '1 Post' : $category->posts->count().' Posts' }} assigned.)</span>
							<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
							 		 data-toggle="collapse" data-target="#collapsep">
						 	</span></a>							
						</h1>
						<div id="collapsep" class="collapse {{ request()->has('pageP') ? 'show' : 'hide' }}" data-parent="#accordionp">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th>#</th>
									<th>Title</th>
									<th>Excerpt</th>
									<th width="120">Updated</th>
									<th width="130" class="text-right">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
								</thead>
								<tbody>						
									@foreach($posts as $post)
										<tr>
											<th>{{ $post->id }}</th>
											<td>{{ $post->title }}</td>
											<td>
												{{ substr(strip_tags($post->excerpt), 0, 156) }}{{ strlen(strip_tags($post->excerpt)) >156 ? '...' : '' }}
											</td>
											<td>{{ date('j M Y', strtotime($post->updated_at)) }}</td>
											<td class="text-right" nowrap>
												<a href="{{ route('posts.show', $post->id)}}" class="btn btn-sm btn-outline-dark">View Post</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<div class="d-flex justify-content-center">
								{{ $posts->appends(Request::all())->render() }} 
							</div>
						</div>	
					</div>
				</div>
			</div>
		@endif		
	@endif
@endsection

@section('scripts')
@endsection
