@extends('manage')

@section('title','| Manage Permissions')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9">
				<h1><a id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="fas fa-user-cog mr-4"></span>
				@endif 				 Manage Permissions
			</a></h1>
		</div>
		<div class="col-md-3">
			<a href="{{ route('permissions.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><span class="fas fa-user-plus mr-2"></span>Create New Permission</a>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
	</div>

	@if($permissions)
		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="thead-dark">
						<th>#</th>
						<th>Name</th>
						<th>Slug</th>
						<th>Description</th>
						<th width="120px">Created At</th>
						<th width="120px">Updated At</th>
						<th width="130px" class="text-right">Page {{$permissions->currentPage()}} of {{$permissions->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($permissions as $permission)
							<tr>
								<th>{{ $permission->id }}</th>
								<td>{{ $permission->display_name }}</td>
								<td>{{ $permission->name }}</td>
								<td>{{ $permission->description }}</td>
								<td>{{ date('j M Y', strtotime($permission->created_at)) }}</td>
								<td>{{ date('j M Y', strtotime($permission->updated_at)) }}</td>
								<td class="text-right">
									<a href="{{ route('permissions.show', $permission->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('permissions.edit', $permission->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $permissions->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif    
@endsection

@section('scripts')
@endsection
