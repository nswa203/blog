@extends('manage')

@section('title','| Manage Roles')

@section('stylesheets')
@endsection

@section('content')
	<div class="row">
		<div class="col-md-9 myWrap">
				<h1><a class="pointer" id="menu-toggle2" data-toggle="tooltip", data-placement="top", title="Toggle NavBar">
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
				<table class="table table-hover table-responsive-lg">
					<thead class="thead-dark" style="color:inherit;">
						<th class="thleft">
							<a href="{{ route('roles.index', ['sort'=>'i'.$sort, 'search'=>$search]) }}">
								<i id="sort-i" class="ml-2"></i><i class="fas fa-hashtag mb-1"></i>
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('roles.index', ['sort'=>'n'.$sort, 'search'=>$search]) }}">
								<i id="sort-n" class="ml-2"></i>Name
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('roles.index', ['sort'=>'s'.$sort, 'search'=>$search]) }}">
								<i id="sort-s" class="ml-2"></i>Slug
							</a>	
						</th>
						<th class="thleft">
							<a href="{{ route('roles.index', ['sort'=>'d'.$sort, 'search'=>$search]) }}">
								<i id="sort-d" class="ml-2"></i>Description
							</a>	
						</th>						
						<th class="thleft" width="120px">
							<a href="{{ route('roles.index', ['sort'=>'c'.$sort, 'search'=>$search]) }}">
								<i id="sort-c" class="ml-2"></i>Created
							</a>	
						</th>
						<th class="thleft" width="120px">
							<a href="{{ route('roles.index', ['sort'=>'u'.$sort, 'search'=>$search]) }}">
								<i id="sort-u" class="ml-2"></i>Updated
							</a>	
						</th>
						<th width="130px" class="text-right">Page {{$roles->currentPage()}} of {{$roles->lastPage()}}</th>
					</thead>
					<tbody>						
						@foreach($roles as $role)
							<tr>
								<th>{{ $role->id }}</th>
								<td>{{ $role->display_name }}</td>
								<td>{{ $role->name }}</td>
								<td>{{ myTrim($role->description, 32) }}</td>
								<td>{{ date('j M Y', strtotime($role->created_at)) }}</td>
								<td>{{ date('j M Y', strtotime($role->updated_at)) }}</td>
								<td class="text-right" nowrap>
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
	{!! Html::script('js/app.js')     !!}
	{!! Html::script('js/helpers.js') !!}

	<script>
		mySortArrow({!! json_encode($sort) !!});
	</script>
@endsection
