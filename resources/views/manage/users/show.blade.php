@extends('manage')

@section('title','| Manage View User')

@section('stylesheets')
@endsection

@section('content')
	@if($user)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-user mr-4"></span>View User Details</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $user->name !!}</p>
				
				<h3>eMail:</h3>
				<p class="lead">{!! $user->email !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('users.show', $user->id) }}">{{ route('users.show', $user->id) }}</a></dd>
						<dt class="col-sm-5">Profile:</dt>
						<dd class="col-sm-7">
							@if($user->profile['id'])
								<a href="{{ route('profiles.show', $user->profile['id']) }}">{{ $user->profile['username'] }}</a>
							@endif
						</dd>							
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($user->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('users.edit', '<i class="fas fa-user-edit mr-2"></i>Edit', [$user->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['users.delete', $user->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('users.index', '<i class="fas fa-user-friends mr-2"></i>See All Users', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		@if($user->albums->count() && $albums)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Albums
						<span class="h1-suffix">(This User has {{ $user->albums->count()==1 ? '1 Album' : $user->albums->count().' Albums' }} assigned.)</span>
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
											<a href="{{ route('categories.show', [$album->category_id, 'Albums']) }}"><span class="badge badge-info">{{ $album->category->name }}</span></a>
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

		@if($user->posts->count() && $posts)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Posts
						<span class="h1-suffix">(This User has {{ $user->posts->count()==1 ? '1 Post' : $user->posts->count().' Posts' }} assigned.)</span>
					</h1>
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
		@endif

		@if($user->roles->count() && $roles)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Roles
						<span class="h1-suffix">(This User has {{ $user->roles->count()==1 ? '1 Role' : $user->roles->count().' Roles' }} assigned.)</span>
					</h1>
						<table class="table table-hover table-responsive-lg">
							<thead class="thead-dark">
								<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
								<th>Name</th>
								<th>Slug</th>
								<th>Description</th>
								<th width="120px">Created</th>
								<th width="120px">Updated</th>
								<th width="130px" class="text-right">Page {{$roles->currentPage()}} of {{$roles->lastPage()}}</th>
							</thead>
							<tbody>						
								@foreach($roles as $role)
									<tr>
										<th>{{ $role->id }}</th>
										<td>{{ $role->display_name }}</td>
										<td>{{ $role->name }}</td>
										<td>{{ substr($role->description, 0, 156) }}{{ strlen($role->description)>156 ? '...' : '' }}
										<td>{{ date('j M Y', strtotime($role->created_at)) }}</td>
										<td>{{ date('j M Y', strtotime($role->updated_at)) }}</td>
										<td class="text-right" nowrap>
											<a href="{{ route('roles.show', $role->id)}}" class="btn btn-sm btn-outline-dark">View Role</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="d-flex justify-content-center">
							{{ $roles->appends(Request::all())->render() }} 
						</div>
					</div>
				</div>
			</div>
		@endif	
	@endif
@endsection

@section('scripts')
@endsection
