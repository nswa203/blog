@extends('manage')

@section('title','| Manage View Post')

@section('stylesheets')
@endsection

@section('content')
	@if($post)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-file-alt mr-4"></span>{{ $post->title }}</a></h1>
				<hr>
				@if($post->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$post->banner) }}" width="100%" />
					</div>
				@endif

				<p class="lead">{!! $post->body !!}</p>
				<hr>
				<div class="tags">
					@foreach ($post->tags as $tag)
						<a href="{{ route('tags.show', $tag->id) }}"><span class="badge badge-info">{{ $tag->name }}</span></a>
					@endforeach
				</div>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ url($post->slug) }}">{{ url($post->slug) }}</a></dd>
						<dt class="col-sm-5">Post ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('posts.show', $post->id) }}">{{ $post->id }}</a></dd>
						<dt class="col-sm-5">Category:</dt>						
						<dd class="col-sm-7">
							<a href="{{ route('categories.show', [$post->category->id, session('zone')]) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
						</dd>
						<dt class="col-sm-5">Published:</dt>						
						<dd class="col-sm-7">
							@if($post->published_at)
								{{ date('j M Y, h:i a', strtotime($post->published_at)) }}
							@else	
								<span class="text-danger">{{ $status_list[$post->status] }}</span>
							@endif	
						</dd>							
						<dt class="col-sm-5">Author:</dt>
						<dd class="col-sm-7">
							@if($post->user->id)
								<a href="{{ route('users.show', $post->user->id) }}">{{ $post->user->name }}</a>
							@endif
						</dd>		
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($post->updated_at)) }}</dd>
					</dl>

					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('posts.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$post->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['posts.delete', $post->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('posts.index', '<i class="fas fa-file-alt mr-2"></i>See All Posts', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
				@if($post->image)
					<img src="{{ asset('images/'.$post->image) }}" width="100%" class="mt-3"/>
				@endif	
			</div>
		</div>	
		
		@if($post->comments->count() && $comments)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Comments
							<span class="h1-suffix">(This Post has {{ $post->comments->count()==1 ? '1 Comment' : $post->comments->count().' Comments' }} assigned.)</span>
							<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
							 		 data-toggle="collapse" data-target="#collapsec">
						 	</span></a>								
						</h1>
						<div id="collapsec" class="collapse {{ request()->has('pageC') ? 'show' : 'hide' }}" data-parent="#accordionc">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>OK</th>
									<th>Name</th>
									<th>eMail</th>
									<th>Comment</th>
									<th width="120px">Created</th>
									<th width="130px" class="text-right">Page {{$comments->currentPage()}} of {{$comments->lastPage()}}</th>
								</thead>
								<tbody>						
									@foreach($comments as $comment)
										<tr>
											<th>{{ $comment->id }}</th>
											<td>
												{!! $comment->approved ? "<span class='fas fa-check text-success'></span>" : "<span class='fas fa-times text-danger'></span>" !!}
											</td>
											<td>{{ $comment->name }}</td>
											<td>{{ $comment->email }}</td>
											<td>
												{{ substr(strip_tags($comment->comment), 0, 256)}}{{ strlen(strip_tags($comment->comment)) >256 ? '...' : '' }}
											</td>
											<td>{{ date('j M Y', strtotime($comment->created_at)) }}</td>
											<td class="text-right" nowrap>
												<a href="{{ route('comments.edit', $comment->id) }}" class="btn btn-sm btn-primary">
													<span class="far fa-edit"></span>
												</a>
												<a href="{{ route('comments.delete', $comment->id) }}" class="btn btn-sm btn-danger">
													<span class="far fa-trash-alt"></span>
												</a>	
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<div class="d-flex justify-content-center">
								{{ $comments->appends(Request::all())->render() }} 
							</div>
						</div>
					</div>
				</div>
			</div>
		@endif

		@if($post->folders->count() && $folders)
			<div class="row mt-3" id="accordionf">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Folders
						<span class="h1-suffix">(This User has {{ $post->folders->count()==1 ? '1 Folder' : $post->folders->count().' Folders' }} assigned.)</span>
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

	@endif
@endsection

@section('scripts')
@endsection
