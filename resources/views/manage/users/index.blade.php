@extends('manage')

@section('title','| Manage Users')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="fas fa-user-cog mr-4"></span>
				@endif 				 Manage Users
			</a></h1>
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
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark">
						<th width="20px"><i class="fas fa-hashtag mb-1 ml-2"></i></th>
						<th>Name</th>
						<th>eMail</th>
						<th>Username</th>
						<th width="120">Created</th>
						<th width="120">Updated</th>
						<th width="130" class="text-right">Page {{$users->currentPage()}} of {{$users->lastPage()}}</th>
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
									<a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $users->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif    
@endsection

@section('scripts')
@endsection
