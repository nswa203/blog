@extends('manage')

@section('title','| Manage View Folder')

@section('stylesheets')
@endsection

@section('content')
	@if($folder)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-folder mr-4"></span>Folder {{ $folder->name }}</a></h1>
				<hr>
				<a href="{{ route('private.getfile', [$folder->id, 'Folder.jpg']) }}">
					<img src="{{ route('private.getfile', [$folder->id, 'Folder.jpg']) }}" xwidth="150px" class="img-frame float-left mr-4" style="margin-top:0px; margin-bottom:10px;"
						onerror="this.onerror=null; this.src='{{ asset('favicon.ico') }}';"
					/>
				</a>
				<p class="lead">{!! $folder->description !!}</p>
				<p>Location: {{ $folder->path }}</p>
				<p>URL: {{ route('private.getfile', [$folder->id, '']) }}</p>
				<p>Size: {{ round($folder->max_size/1000000) }}M</p>
				<p>Used: {{ round(($folder->size / $folder->max_size) * 100, 2) }}%</p>
				<hr>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					
					@include('partials.__foldersMeta')

					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('folders.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$folder->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['folders.delete', $folder->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
						{!! Html::decode(link_to_route('folders.index', '<i class="fas fa-folder mr-2"></i>See All Folders', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>						
					</div>
				</div>
				@if($folder->image)
					{{-- <img src="{{ asset('images/'.$folder->image) }}" width="100%" class="mt-3"/> --}}
				@endif	
			</div>
		</div>

		@if($folder->posts->count() && $posts)
			<div class="row mt-3" id="accordionp">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Posts
						<span class="h1-suffix">(This Folder has {{ $folder->posts->count()==1 ? '1 Post' : $folder->posts->count().' Posts' }} assigned.)</span>
						<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
						 		 data-toggle="collapse" data-target="#collapsep">
					 	</span></a>							
					</h1>
						<div id="collapsep" class="collapse {{ request()->has('pageP') ? 'show' : 'hide' }}" data-parent="#accordionp">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>Title</th>
									<th>Excerpt</th>
									<th>Category</th>
									<th>Author</th>
									<th width="120px">Published</th>
									<th class="text-right" width="130px">Page {{$posts->currentPage()}} of {{$posts->lastPage()}}</th>
								</thead>
								<tbody>
									@foreach($posts as $post)
										<tr>
											<th>{{ $post->id }}</th>
											<td>{{ $post->title }}</td>
											<td>
												{{ substr(strip_tags($post->excerpt), 0, 156) }}{{ strlen(strip_tags($post->excerpt)) >156 ? '...' : '' }}
											</td>
											<td>
												<a href="{{ route('categories.show', [$post->category_id, 'Posts']) }}"><span class="badge badge-info">{{ $post->category->name }}</span></a>
											</td>
											<td>
												@if($post->user->id)
													<a href="{{ route('users.show', $post->user->id) }}">{{ $post->user->name }}</a>
												@endif	
											</td>
											<th>
												@if($post->published_at)
													<span class="text-success">{{ date('j M Y', strtotime($post->published_at)) }}</span>
												@else	
													<span class="text-danger">{{ $status_list[$post->status] }}</span>
												@endif	
											</th>
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

		@if($folder->profiles->count() && $profiles)
			<div class="row mt-3" id="accordionp">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Profiles
						<span class="h1-suffix">(This Folder has {{ $folder->profiles->count()==1 ? '1 Profile' : $folder->profiles->count().' Profiles' }} assigned.)</span>
						<a><span class="pointer-expand fas fa-chevron-circle-down float-right mr-1"
						 		 data-toggle="collapse" data-target="#collapsepr">
					 	</span></a>							
					</h1>
						<div id="collapsepr" class="collapse {{ request()->has('pagePr') ? 'show' : 'hide' }}" data-parent="#accordionpr">				
							<table class="table table-hover table-responsive-lg">
								<thead class="thead-dark">
									<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
									<th>Name</th>
									<th>eMail</th>
									<th>Username</th>
									<th width="120px">Created</th>
									<th width="120px">Updated</th>
									<th class="text-right" width="130px">Page {{$profiles->currentPage()}} of {{$profiles->lastPage()}}</th>
								</thead>
								<tbody>
									@foreach($profiles as $profile)
										<tr>
											<th>{{ $profile->id }}</th>
											<td>{{ $profile->user->name }}</td>
											<td>{{ $profile->user->email }}</td>
											<td>{{ $profile->username }}</td>
											<td>{{ date('j M Y', strtotime($profile->created_at)) }}</td>
											<td>{{ date('j M Y', strtotime($profile->updated_at)) }}</td>
											<td class="text-right" nowrap>
												<a href="{{ route('profiles.show', $profile->id)}}" class="btn btn-sm btn-outline-dark">View Profile</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							<div class="d-flex justify-content-center">
								{{ $profiles->appends(Request::all())->render() }} 
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
