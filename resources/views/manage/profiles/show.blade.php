@extends('manage')

@section('title','| Manage View Profile')

@section('stylesheets')
@endsection

@section('content')
	@if($profile)
		<div class="row">
			<div class="col-md-8">
				<h1><a class="pointer" id="menu-toggle2"><span class="fas fa-user-circle mr-4"></span>View Profile</a></h1>
				<hr>
				@if($profile->banner)
					<div class="image-crop-height mt-3 mb-5" style="--croph:232px">
						<img src="{{ asset('images/'.$profile->banner) }}" width="100%" />
					</div>
				@endif

				<h3>Name:</h3>
				<p class="lead">{!! $profile->user->name !!}</p>
				
				<h3>eMail:</h3>
				<p class="lead">{!! $profile->user->email !!}</p>

				<h3>Username:</h3>
				<p class="lead">{!! $profile->username !!}</p>

				<h3>About me:</h3>
				<p class="lead">{!! $profile->about_me !!}</p>

				<h3>Phone:</h3>
				<p class="lead">{!! $profile->phone !!}</p>

				<h3>Address:</h3>
				<p class="lead">{!! $profile->address !!}</p>				
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('profiles.show', $profile->id) }}">{{ route('profiles.show', $profile->id) }}</a></dd>
						<dt class="col-sm-5">User:</dt>
						<dd class="col-sm-7"><a href="{{ route('users.show', $profile->user->id) }}">{{ $profile->user->name }}</a></dd>							
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($profile->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($profile->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('profiles.edit', '<i class="fas fa-edit mr-2"></i>Edit', [$profile->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['profiles.delete', $profile->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('profiles.index', '<i class="fas fa-user-circle mr-2"></i>See All Profiles', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>

				@if($profile->image)
					<div class="mt-3">
						<div id="image" style="display:block">
							<img src="{{ asset('images/'.$profile->image) }}" width="100%" />
						</div>
					</div>
				@endif

			</div>
		</div>

		@if($folders->count())
			<div class="row mt-3" id="accordionf">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Folders
						<span class="h1-suffix">(This User has {{ $profile->folders->count()==1 ? '1 Folder' : $profile->folders->count().' Folders' }} assigned.)</span>
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
