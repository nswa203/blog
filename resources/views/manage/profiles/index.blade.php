@extends('manage')

@section('title','| Manage Profiles')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
				@if (isset($search)) <span class="fas fa-search mr-4"></span>
				@else 				 <span class="fas fa-user-circle mr-4"></span>
				@endif 				 Manage Profiles
			</a></h1>
		</div>

		<div class="col-md-3">
			<a href="{{ route('profiles.create', '0') }}" class="btn btn-lg btn-block btn-primary btn-h1-spacing"><span class="fas fa-plus-circle mr-2"></span>Create New Profile</a>
		</div>
		<div class="col-md-12">
			<hr>
		</div>
	</div>

	@if($profiles)
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
						<th width="130" class="text-right">Page {{$profiles->currentPage()}} of {{$profiles->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($profiles as $profile)
							<tr>
								<th>{{ $profile->id }}</th>
								<td><a href="{{ route('users.show', $profile->user->id) }}">{{ $profile->user->name }}</a></td>
								<td>{{ $profile->user->email }}</td>
								<td>{{ $profile->username }}</td>
								<td>{{ date('j M Y', strtotime($profile->created_at)) }}</td>
								<td>{{ date('j M Y', strtotime($profile->updated_at)) }}</td>
								<td class="text-right" nowrap>
									<a href="{{ route('profiles.show', $profile->id)}}" class="btn btn-sm btn-outline-dark">View</a>
									<a href="{{ route('profiles.edit', $profile->id)}}" class="btn btn-sm btn-outline-dark">Edit</a>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<div class="d-flex justify-content-center">
					{{ $profiles->appends(Request::only(['search']))->render() }} 
				</div>
			</div>
		</div>
	@endif    
@endsection

@section('scripts')
@endsection
