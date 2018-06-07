@extends('manage')

@section('title','| Manage View Permission')

@section('stylesheets')
@endsection

@section('content')
	@if($permission)
		<div class="row">
			<div class="col-md-8">
				<h1><a id="menu-toggle2"><span class="fas fa-user mr-4"></span>View Permission</a></h1>
				<hr>
				<h3>Name:</h3>
				<p class="lead">{!! $permission->display_name !!}</p>
				
				<h3>Slug:</h3>
				<p class="lead">{!! $permission->name !!}</p>

				<h3>Description:</h3>
				<p class="lead">{!! $permission->description !!}</p>
			</div>	

			<div class="col-md-4">
				<div class="card card-body bg-light">
					<dl class="row">
						<dt class="col-sm-5">URL:</dt>
						<dd class="col-sm-7"><a href="{{ route('permissions.show', $permission->id) }}">{{ route('permissions.show', $permission->id) }}</a></dd>
						<dt class="col-sm-5">Permission ID:</dt>
						<dd class="col-sm-7"><a href="{{ route('permissions.show', $permission->id) }}">{{ $permission->id }}</a></dd>							
						<dt class="col-sm-5">Created At:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->created_at)) }}</dd>
						<dt class="col-sm-5">Last Updated:</dt>
						<dd class="col-sm-7">{{ date('j M Y, h:i a', strtotime($permission->updated_at)) }}</dd>
					</dl>
					<hr class="hr-spacing-top">
					<div class="row">
						<div class="col-sm-6">
							{!! Html::decode(link_to_route('permissions.edit', '<i class="fas fa-user-edit mr-2"></i>Edit', [$permission->id], ['class'=>'btn btn-primary btn-block'])) !!}
						</div>
						<div class="col-sm-6">
							{!! Form::open(['route'=>['permissions.delete', $permission->id], 'method'=>'GET']) !!}
								{{ Form::button('<i class="fas fa-trash-alt mr-2"></i>Delete', ['type'=>'submit', 'class'=>'btn btn-danger btn-block']) }}
							{!! Form::close() !!}
						</div>
					</div>
					<div class="row mt-3">
						<div class="col-sm-12">
							{!! Html::decode(link_to_route('permissions.index', '<i class="fas fa-user-friends mr-2"></i>See All Permissions', [], ['class'=>'btn btn-outline-dark btn-block'])) !!}
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row mt-3">
			<div class="col-md-12">
				<div class="card card-body bg-light">
					<h1>
						Roles
						<span class="h1-suffix">(This Permission is associated with {{ $roles->count()==1 ? '1 Role.' : $roles->count().' Roles.' }})</span>
					</h1>
					<table class="table table-hover">
						<thead class="thead-dark">
							<th>#</th>
							<th>Name</th>
							<th>Slug</th>
							<th>Description</th>
							<th width="120px">Updated At</th>
							<th width="130px" class="text-right">Page {{$roles->currentPage()}} of {{$roles->lastPage()}}</th>
						</thead>
						<tbody>	
							@foreach($roles as $role)
								<tr>
									<th>{{ $role->id }}</th>
									<td>{{ $role->display_name }}</td>
									<td>{{ $role->name }}</td>
									<td>{{ $role->description }}</td>
									<td>{{ date('j M Y', strtotime($role->updated_at)) }}</td>
									<td class="text-right">
										<a href="{{ route('roles.show', $role->id)}}" class="btn btn-sm btn-outline-dark">View Role</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					<div class="d-flex justify-content-center">
						{!! $roles->render() !!} 
					</div>
				</div>
			</div>
		</div>					
	@endif
@endsection

@section('scripts')
@endsection
