@extends('manage')

@section('title','| Manage Roles')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9">
				<h1><a id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="fas fa-user-cog mr-4"></span>
				@endif 				 Manage Roles
			</a></h1>
		</div>

		<div class="col-md-3">
			<a href="{{ route('roles.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><span class="fas fa-user-plus mr-2"></span>Create New Role</a>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
	</div>

	@if($roles)
		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Name</th>
						<th>Slug</th>
						<th>Description</th>
						<th width="120px">Created At</th>
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
								<td>{{ date('j M Y', strtotime($role->created_at)) }}</td>
								<td>{{ date('j M Y', strtotime($role->updated_at)) }}</td>
								<td class="text-right">
									<a href="{{ route('roles.show', $role->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('roles.edit', $role->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $roles->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif    
@endsection

@section('scripts')
@endsection
