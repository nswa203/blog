@extends('manage')

@section('title','| Manage Users')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9">
			<h1><a id="menu-toggle2"><span class="fas fa-user-cog mr-4"></span>Manage Users</a></h1>
		</div>

		<div class="col-md-3">
			<a href="{{ route('users.create') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><span class="fas fa-user-plus mr-2"></span>Create New User</a>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
	</div>

	@if($users)
		<div class="row mt-3">
			<div class="col-md-12">
				<table class="table table-hover">
					<thead class="thead-dark">
						<th>#</th>
						<th>Name</th>
						<th>eMail</th>
						<th width="120">Created At</th>
						<th width="120">Updated At</th>
						<th width="130" class="text-right">Page {{$users->currentPage()}} of {{$users->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($users as $user)
							<tr>
								<th>{{ $user->id }}</th>
								<td>{{ $user->name }}</td>
								<td>{{ $user->email }}</td>
								<td>{{ date('j M Y', strtotime($user->created_at)) }}</td>
								<td>{{ date('j M Y', strtotime($user->updated_at)) }}</td>
								<td class="text-right">
									<a href="{{ route('users.show', $user->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('users.edit', $user->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{!! $users->render() !!}
				</div>
			</div>
		</div>
	@endif    
@endsection

@section('scripts')
@endsection
