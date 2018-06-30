@extends('manage')

@section('title','| Manage View Role')

@section('stylesheets')
@endsection

@section('content')
	@if($role)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-user mr-4"></span>View Role</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $role->display_name !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $role->name !!}</p>

				<h3>Description:</h3>
				<p class="lead">{!! $role->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row dd-nowrap">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ route('roles.show', $role->id) }}</a></dd>
						<dt class="col-sm-5">Role ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('roles.show', $role->id) }}">{{ $role->id }}</a></dd>							
						<dt class="col-sm-5">Created:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($role->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('roles.edit', '<i class="fas fa-user-edit mr-2"></i>Edit', [$role->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['roles.delete', $role->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('roles.index', '<i class="fas fa-user-friends mr-2"></i>See All Roles', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		@if($role->permissions->count() && $permissions)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
					<h1>
						Permissions
						<span class="h1-suffix">(This Role has {{ $role->permissions->count()==1 ? '1 Permission' : $role->permissions->count().' Permissions' }} assigned.)</span>
					</h1>
						<table class="table table-hover table-responsive-lg">
							<thead class="thead-dark">
								<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
								<th>Name</th>
								<th>Slug</th>
								<th>Description</th>
								<th width="120px">Created</th>
								<th width="120px">Updated</th>
								<th width="130px" class="text-right">Page {{$permissions->currentPage()}} of {{$permissions->lastPage()}}</th>
							</thead>
							<tbody>						
								@foreach($permissions as $permission)
									<tr>
										<th>{{ $permission->id }}</th>
										<td>{{ $permission->display_name }}</td>
										<td>{{ $permission->name }}</td>
										<td>
											{{ substr($permission->description, 0, 156) }}{{ strlen($permission->description)>156 ? '...' : '' }}
										</td>
										<td>{{ date('j M Y', strtotime($permission->created_at)) }}</td>
										<td>{{ date('j M Y', strtotime($permission->updated_at)) }}</td>
										<td class="text-right" nowrap>
											<a href="{{ route('permissions.show', $permission->id)}}" class="btn btn-sm btn-outline-dark">View Permission</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="d-flex justify-content-center">
							{{ $permissions->appends(Request::all())->render() }} 
						</div>
					</div>
				</div>
			</div>
		@endif

		@if($role->users->count() && $users)
			<div class="row mt-3">
				<div class="col-md-12">
					<div class="card card-body bg-light">
						<h1>
							Users
							<span class="h1-suffix">(This Role is associated with {{ $users->count()==1 ? '1 User.' : $users->count().' Users.' }})</span>
						</h1>
						<table class="table table-hover table-responsive-lg">
							<thead class="thead-dark">
								<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
								<th>Name</th>
								<th>eMail</th>
								<th>Username</th>
								<th width="120px">Created</th>
								<th width="120px">Updated</th>
								<th width="130px" class="text-right">Page {{$users->currentPage()}} of {{$users->lastPage()}}</th>
							</thead>
							<tbody>	
								@foreach($users as $user)
									<tr>
										<th>{{ $user->id }}</th>
										<td>{{ $user->name }}</td>
										<td>{{ $user->email }}</td>
										<td>
											@if($user->profile['id'])
												<a href="{{ route('profiles.show', $user->profile['id']) }}">{{ $user->profile['username'] }}</a>
											@else
												{{ $user->profile['username'] }}
											@endif
										</td>
										<td>{{ date('j M Y', strtotime($user->created_at)) }}</td>
										<td>{{ date('j M Y', strtotime($user->updated_at)) }}</td>
										<td class="text-right" nowrap>
											<a href="{{ route('users.show', $user->id)}}" class="btn btn-sm btn-outline-dark">View User</a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						<div class="d-flex justify-content-center">
							{{ $users->appends(Request::all())->render() }} 
						</div>
					</div>
				</div>
			</div>
		@endif	
	@endif
@endsection

@section('scripts')
@endsection
