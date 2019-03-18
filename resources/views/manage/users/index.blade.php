@extends('manage')

@section('title','| Manage Users')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9 myWrap">
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
					<thead class="thead-dark" style="color:inherit;">
						<th class="thleft" width="40px">
							<a href="{{ route('users.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
								<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('users.index', ['sort'=>'n'.$sort, 'search'=>$search]) }}">
								<i id="sort-n" class="ml-2"></i>Name
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('users.index', ['sort'=>'e'.$sort, 'search'=>$search]) }}">
								<i id="sort-e" class="ml-2"></i>eMail
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('users.index', ['sort'=>'p'.$sort, 'search'=>$search]) }}">
								<i id="sort-p" class="ml-2"></i>Username
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('users.index', ['sort'=>'c'.$sort, 'search'=>$search]) }}">
								<i id="sort-c" class="ml-2"></i>Created
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('users.index', ['sort'=>'u'.$sort, 'search'=>$search]) }}">
								<i id="sort-u" class="ml-2"></i>Updated
							</a>	
						</th>
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
					{{ $users->appends(Request::only(['search', 'sort']))->render() }} 
				</div>
			</div>
		</div>
	@endif    
@endsection

@section('scripts')
	{!! Html::script('js/app.js')     !!}
	{!! Html::script('js/helpers.js') !!}

	<script>
		mySortArrow({!! json_encode($sort) !!});
	</script>
@endsection
